<?php

namespace App\GameActions;

use App\Contracts\GameActionInterface;
use App\Enums\GameStateChangeType;
use App\Events\GameStateChanged;
use App\Http\Requests\ExecuteGameActionRequest;
use App\Models\Game;
use App\Models\GameHex;
use App\Models\GamePlayer;
use App\Models\Unit;

class MoveAction implements GameActionInterface
{
    public function getCost(): int
    {
        return 1;
    }

    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        $validated = $request->validated();
        $unit = Unit::findOrFail($validated['unit_id']);

        if ($unit->player_id !== $player->id) {
            throw new \Exception('Unit not owned by player');
        }

        $targetHex = GameHex::where('game_id', $game->id)
            ->where('q', $validated['target_hex_q'])
            ->where('r', $validated['target_hex_r'])
            ->firstOrFail();

        if (!$unit->canReachHex($targetHex) || $targetHex->unit) {
            throw new \Exception('Invalid move');
        }

        $oldHex = $unit->hex;
        $cost = $oldHex->distanceTo($targetHex) * $unit->getMovementCost($targetHex);
        $unit->update(['hex_id' => $targetHex->id, 'movement_points' => $unit->movement_points - $cost]);

        GameStateChanged::dispatch($game, GameStateChangeType::UNIT_MOVED, ['unit' => $unit, 'from_hex' => $oldHex, 'to_hex' => $targetHex]);

        return ['movement_points_remaining' => $unit->movement_points];
    }
}