<?php

namespace App\GameActions;

use App\Contracts\GameActionInterface;
use App\Contracts\RecruiterInterface;
use App\Enums\GameStateChangeType;
use App\Enums\UnitType;
use App\Events\GameStateChanged;
use App\Http\Requests\ExecuteGameActionRequest;
use App\Models\Building;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Unit;

class RecruitAction implements GameActionInterface
{
    public function getCost(): int
    {
        return 2;
    }

    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        $validated = $request->validated();
        $building = Building::findOrFail($validated['building_id']);
        $unitType = UnitType::from($validated['action_data']['unit_type']);
        $unitConfig = config('game.units.' . $unitType->value);

        if (!$unitConfig) {
            throw new \Exception('Invalid unit type specified.');
        }

        if ($building->player_id !== $player->id) {
            throw new \Exception('Building not owned by player');
        }

        if (!$building->canRecruit($unitType)) {
            throw new \Exception('Cannot recruit unit from this building');
        }

        $costs = $unitConfig['costs'];
        if (!$player->hasResources($costs)) {
            throw new \Exception('Not enough resources to recruit unit.');
        }

        $player->spendResources($costs);

        $unitStats = $unitConfig['stats'];
        $unit = Unit::create([
            'game_id' => $game->id,
            'player_id' => $player->id,
            'hex_id' => $building->hex_id,
            'type' => $unitType,
            'name' => $unitConfig['name'],
            'level' => 1,
            'health' => $unitStats['health'],
            'max_health' => $unitStats['health'],
            'attack' => $unitStats['attack'],
            'defense' => $unitStats['defense'],
            'movement_range' => $unitStats['movement_range'],
            'movement_points' => $unitStats['movement_range'],
            'max_movement_points' => $unitStats['movement_range'],
            'costs' => $costs,
        ]);

        GameStateChanged::dispatch($game, GameStateChangeType::UNIT_RECRUITED, compact('unit', 'building'));
        GameStateChanged::dispatch($game, GameStateChangeType::RESOURCES_UPDATED, [
            'player_id' => $player->id,
            'resources' => $player->only(['gold', 'food', 'wood', 'stone', 'iron']),
        ]);

        return ['unit_id' => $unit->id];
    }
}
