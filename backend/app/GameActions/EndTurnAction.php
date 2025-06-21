<?php

namespace App\GameActions;

use App\Contracts\GameActionInterface;
use App\Enums\GameStateChangeType;
use App\Events\GameStateChanged;
use App\Events\GameTurnStarted;
use App\Http\Requests\ExecuteGameActionRequest;
use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Support\Facades\DB;

class EndTurnAction implements GameActionInterface
{
    public function getCost(): int
    {
        return 0;
    }

    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        DB::transaction(function () use ($game, $player) {
            // 1. Reset unit movement points
            $game->units()->where('player_id', $player->id)->get()->each->resetMovementPoints();

            // 2. Collect resources from buildings
            $this->collectResources($player);

            // 3. Complete the current turn
            $currentTurn = $game->turns()->where('status', 'active')->firstOrFail();
            $currentTurn->update(['status' => 'completed', 'ended_at' => now()]);

            // 4. Determine next player and start new turn
            $players = $game->players()->whereNull('left_at')->orderBy('turn_order')->get();
            $currentPlayerIndex = $players->search(fn($p) => $p->id === $player->id);
            $nextPlayerIndex = ($currentPlayerIndex + 1) % $players->count();
            $nextPlayer = $players[$nextPlayerIndex];

            $nextTurnNumber = $game->current_turn + 1;
            $game->update(['current_turn' => $nextTurnNumber]);

            $game->turns()->create([
                'turn_number' => $nextTurnNumber,
                'player_id' => $nextPlayer->id,
                'status' => 'active',
                'started_at' => now(),
                'time_remaining' => $game->turn_time_limit,
            ]);

            // 5. Dispatch events
            GameTurnStarted::dispatch($game, $nextPlayer, $nextTurnNumber, $game->turn_time_limit);
        });

        return ['message' => 'Turn ended successfully.'];
    }

    private function collectResources(GamePlayer $player): void
    {
        $resourceGains = [];

        $player->buildings()->whereNull('destroyed_at')->each(function ($building) use (&$resourceGains) {
            $buildingConfig = config('game.buildings.' . $building->type->value);
            if (!empty($buildingConfig['produces'])) {
                foreach ($buildingConfig['produces'] as $resource => $amount) {
                    $resourceGains[$resource] = ($resourceGains[$resource] ?? 0) + $amount;
                }
            }
        });

        if (!empty($resourceGains)) {
            foreach ($resourceGains as $resource => $amount) {
                $player->{$resource} += $amount;
            }
            $player->save();

            GameStateChanged::dispatch($player->game, GameStateChangeType::RESOURCES_UPDATED, [
                'player_id' => $player->id,
                'resources' => $player->only(array_keys($resourceGains)),
            ]);
        }
    }
}
