<?php

namespace App\Modules\Campaign\Controllers;

use App\Modules\Campaign\Models\Campaign;
use App\Modules\Campaign\Models\CampaignPlayer;
use Illuminate\Http\Request;
use App\Modules\Common\Controllers\Controller;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = Campaign::with(['creator', 'players'])
            ->where('status', '!=', 'finished')
            ->paginate(20);

        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_players' => 'required|integer|min:2|max:8',
        ]);

        $campaign = Campaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'max_players' => $request->max_players,
            'current_players' => 1,
            'status' => 'waiting',
            'created_by' => $request->user()->id,
            'settings' => $request->settings ?? [],
        ]);

        // Creator automatically joins the campaign
        CampaignPlayer::create([
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()->id,
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return response()->json($campaign->load(['creator', 'players']), 201);
    }

    public function show(Campaign $campaign)
    {
        return response()->json($campaign->load(['creator', 'players.user']));
    }

    public function join(Request $request, Campaign $campaign)
    {
        if ($campaign->current_players >= $campaign->max_players) {
            return response()->json(['error' => 'Campaign is full'], 400);
        }

        if ($campaign->status !== 'waiting') {
            return response()->json(['error' => 'Campaign is not accepting new players'], 400);
        }

        $existingPlayer = CampaignPlayer::where('campaign_id', $campaign->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingPlayer) {
            return response()->json(['error' => 'Already joined this campaign'], 400);
        }

        CampaignPlayer::create([
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()->id,
            'role' => 'player',
            'joined_at' => now(),
        ]);

        $campaign->increment('current_players');

        return response()->json(['message' => 'Successfully joined campaign']);
    }

    public function leave(Request $request, Campaign $campaign)
    {
        $player = CampaignPlayer::where('campaign_id', $campaign->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$player) {
            return response()->json(['error' => 'Not a member of this campaign'], 400);
        }

        $player->delete();
        $campaign->decrement('current_players');

        return response()->json(['message' => 'Successfully left campaign']);
    }

    public function start(Request $request, Campaign $campaign)
    {
        if ($campaign->created_by !== $request->user()->id) {
            return response()->json(['error' => 'Only campaign creator can start the game'], 403);
        }

        if ($campaign->status !== 'waiting') {
            return response()->json(['error' => 'Campaign is not in waiting status'], 400);
        }

        $campaign->update(['status' => 'active']);

        return response()->json(['message' => 'Campaign started successfully']);
    }
}
