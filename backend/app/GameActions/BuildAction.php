<?php

namespace App\GameActions;

use App\Contracts\GameActionInterface;
use App\Enums\BuildingType;
use App\Enums\GameStateChangeType;
use App\Events\GameStateChanged;
use App\Http\Requests\ExecuteGameActionRequest;
use App\Models\Building;
use App\Models\Game;
use App\Models\GameHex;
use App\Models\GamePlayer;

class BuildAction implements GameActionInterface
{
    public function getCost(): int
    {
        return 3;
    }

    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        $validated = $request->validated();
        $targetHex = GameHex::where('game_id', $game->id)
            ->where('q', $validated['target_hex_q'])
            ->where('r', $validated['target_hex_r'])
            ->firstOrFail();

        $buildingType = BuildingType::from($validated['action_data']['building_type']);
        $buildingConfig = config('game.buildings.' . $buildingType->value);

        if (!$buildingConfig) {
            throw new \Exception('Invalid building type specified.');
        }

        if ($targetHex->building || $targetHex->unit) {
            throw new \Exception('Invalid build location');
        }

        $costs = $buildingConfig['costs'];
        if (!$player->hasResources($costs)) {
            throw new \Exception('Not enough resources to build.');
        }

        $player->spendResources($costs);

        $building = Building::create([
            'game_id' => $game->id,
            'player_id' => $player->id,
            'hex_id' => $targetHex->id,
            'type' => $buildingType,
            'name' => $buildingConfig['name'],
            'level' => 1,
            'health' => 100, // Or get from config
            'max_health' => 100, // Or get from config
            'production_rate' => 10, // Or get from config
            'storage_capacity' => 100, // Or get from config
            'defense_bonus' => 0, // Or get from config
            'is_capital' => false,
            'constructed_at' => now(),
            'costs' => $costs,
        ]);

        GameStateChanged::dispatch($game, GameStateChangeType::BUILDING_CONSTRUCTED, compact('building', 'targetHex'));
        GameStateChanged::dispatch($game, GameStateChangeType::RESOURCES_UPDATED, [
            'player_id' => $player->id,
            'resources' => $player->only(['gold', 'food', 'wood', 'stone', 'iron']),
        ]);

        return ['building_id' => $building->id];
    }
}
