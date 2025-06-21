<?php

namespace App\GameActions;

use App\Contracts\GameActionInterface;
use App\Contracts\UpgradableInterface;
use App\Enums\GameStateChangeType;
use App\Events\GameStateChanged;
use App\Http\Requests\ExecuteGameActionRequest;
use App\Models\Building;
use App\Models\Game;
use App\Models\GamePlayer;

class UpgradeAction implements GameActionInterface
{
    public function getCost(): int
    {
        return 2;
    }

    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        $validated = $request->validated();
        $building = Building::findOrFail($validated['building_id']);

        if ($building->player_id !== $player->id) {
            throw new \Exception('Building not owned by player');
        }

        if (!$building->canUpgrade()) {
            throw new \Exception('Cannot upgrade building');
        }

        $oldLevel = $building->level;
        $building->update([
            'level' => $building->level + 1,
            'health' => $building->max_health + 20,
            'max_health' => $building->max_health + 20,
        ]);

        GameStateChanged::dispatch($game, GameStateChangeType::BUILDING_UPGRADED, ['building' => $building, 'old_level' => $oldLevel, 'new_level' => $building->level]);

        return ['new_level' => $building->level];
    }
}
