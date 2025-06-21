<?php

namespace App\Services;

use App\Enums\GameActionType;
use App\Enums\GameStateChangeType;
use App\Enums\BuildingType;
use App\Enums\UnitType;
use App\Events\GameActionExecuted;
use App\Events\GameStateChanged;
use App\Models\Game;
use App\Models\GameAction;
use App\Models\GameHex;
use App\Models\GamePlayer;
use App\Models\Unit;
use App\Models\Building;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ExecuteGameActionRequest;

class GameActionService
{
    public function __construct(protected GameActionFactory $actionFactory)
    {
    }

    public function executeAction(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array
    {
        try {
            $validated = $request->validated();
            $actionType = GameActionType::from($validated['action_type']);
            $actionHandler = $this->actionFactory->make($actionType);

            $actionPointsCost = $actionHandler->getCost();
            $remainingPoints = $game->getRemainingActionPoints($player);

            if ($remainingPoints < $actionPointsCost) {
                throw new \Exception('Not enough action points');
            }

            $action = null;
            $result = DB::transaction(function () use ($game, $player, $request, $actionHandler, $actionPointsCost, $actionType, &$action) {
                $action = GameAction::create([
                    'game_id' => $game->id,
                    'player_id' => $player->id,
                    'turn_number' => $game->current_turn,
                    'action_type' => $actionType,
                    'action_points_cost' => $actionPointsCost,
                    'source_hex_id' => $this->getHexId($game, $request->input('source_hex_q'), $request->input('source_hex_r')),
                    'target_hex_id' => $this->getHexId($game, $request->input('target_hex_q'), $request->input('target_hex_r')),
                    'unit_id' => $request->input('unit_id'),
                    'building_id' => $request->input('building_id'),
                    'action_data' => $request->input('action_data', []),
                    'successful' => true,
                    'executed_at' => now(),
                ]);

                $result = $actionHandler->execute($game, $player, $request);

                DB::afterCommit(function () use ($action) {
                    if ($action) {
                        GameActionExecuted::dispatch($action);
                    }
                });

                return $result;
            });

            if (!$action) {
                // This case should ideally not be reached if the transaction is successful.
                throw new \Exception('Game action was not created.');
            }

            return [
                'message' => 'Action executed successfully',
                'action' => $action->load(['sourceHex', 'targetHex', 'unit', 'building']),
                'result' => $result,
                'remaining_action_points' => $game->getRemainingActionPoints($player),
            ];
        } catch (\Exception $e) {
            if (isset($action) && $action && $action->exists) {
                $action->update(['successful' => false, 'error_message' => $e->getMessage()]);
                DB::afterCommit(function () use ($action) {
                    if ($action) {
                        GameActionExecuted::dispatch($action);
                    }
                });
            }
            throw $e;
        }
    }

    private function getHexId(Game $game, ?int $q, ?int $r): ?int
    {
        return ($q === null || $r === null) ? null : $game->hexes()->where('q', $q)->where('r', $r)->value('id');
    }
}
