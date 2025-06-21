<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function __construct(
        private GameService $gameService
    ) {}

    public function index(): JsonResponse
    {
        $games = Game::with(['players.user', 'creator'])
            ->where('status', '!=', 'finished')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($games);
    }

    public function store(StoreGameRequest $request): JsonResponse
    {
        $game = $this->gameService->createGame($request->validated(), Auth::user());
        return response()->json($game->load(['players.user', 'creator']), 201);
    }

    public function show(Game $game): JsonResponse
    {
        $game->load(['players.user', 'creator', 'currentTurn']);
        $player = $game->players()->where('user_id', Auth::id())->first();

        return response()->json([
            'game' => $game,
            'is_player' => (bool)$player,
            'is_my_turn' => $game->isPlayerTurn(Auth::user()),
            'remaining_action_points' => $player ? $game->getRemainingActionPoints($player) : 0,
        ]);
    }

    public function join(Game $game): JsonResponse
    {
        try {
            $player = $this->gameService->joinGame($game, Auth::user());
            return response()->json($player->load('user'));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function leave(Game $game): JsonResponse
    {
        try {
            $this->gameService->leaveGame($game, Auth::user());
            return response()->json(['message' => 'Left game successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function start(Game $game): JsonResponse
    {
        try {
            $this->gameService->startGame($game, Auth::user());
            return response()->json(['message' => 'Game started successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getMap(Game $game): JsonResponse
    {
        if (!$game->players()->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'Not a player in this game'], 403);
        }

        $hexes = $game->hexes()->with(['building', 'unit'])->get()->map(fn ($hex) => [
            'id' => $hex->id, 'q' => $hex->q, 'r' => $hex->r, 'terrain_type' => $hex->terrain_type,
            'building' => $hex->building ? ['id' => $hex->building->id, 'type' => $hex->building->type, 'player_id' => $hex->building->player_id] : null,
            'unit' => $hex->unit ? ['id' => $hex->unit->id, 'type' => $hex->unit->type, 'player_id' => $hex->unit->player_id] : null,
        ]);

        return response()->json(['game_id' => $game->id, 'map_size' => $game->map_size, 'hexes' => $hexes]);
    }

    public function getHex(Game $game, int $q, int $r): JsonResponse
    {
        if (!$game->players()->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'Not a player in this game'], 403);
        }

        $hex = $game->hexes()->where('q', $q)->where('r', $r)->with(['building', 'unit'])->first();
        if (!$hex) {
            return response()->json(['message' => 'Hex not found'], 404);
        }

        return response()->json($hex);
    }
}
