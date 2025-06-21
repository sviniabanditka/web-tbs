<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExecuteGameActionRequest;
use App\Services\GameActionService;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class GameActionController extends Controller
{
    public function __construct(protected GameActionService $gameActionService)
    {
    }

    public function execute(ExecuteGameActionRequest $request, Game $game): JsonResponse
    {
        $player = $game->players()->where('user_id', $request->user()->id)->firstOrFail();

        try {
            $result = $this->gameActionService->executeAction($game, $player, $request);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
