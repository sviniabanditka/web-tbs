<?php

namespace App\Events;

use App\Models\GameAction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameActionExecuted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action;

    public function __construct(GameAction $action)
    {
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("game.{$this->action->game_id}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'action.executed';
    }

    public function broadcastWith(): array
    {
        $data = [
            'action_id' => $this->action->id,
            'game_id' => $this->action->game_id,
            'player_id' => $this->action->player_id,
            'turn_number' => $this->action->turn_number,
            'action_type' => $this->action->action_type,
            'action_points_cost' => $this->action->action_points_cost,
            'successful' => $this->action->successful,
            'executed_at' => $this->action->executed_at,
            'action_description' => $this->action->action_description,
            'action_points_remaining' => $this->action->action_points_remaining,
        ];

        // Add action-specific data
        if ($this->action->sourceHex) {
            $data['source_hex'] = [
                'id' => $this->action->sourceHex->id,
                'q' => $this->action->sourceHex->q,
                'r' => $this->action->sourceHex->r,
            ];
        }

        if ($this->action->targetHex) {
            $data['target_hex'] = [
                'id' => $this->action->targetHex->id,
                'q' => $this->action->targetHex->q,
                'r' => $this->action->targetHex->r,
            ];
        }

        if ($this->action->unit) {
            $data['unit'] = [
                'id' => $this->action->unit->id,
                'type' => $this->action->unit->type,
                'name' => $this->action->unit->name,
                'health' => $this->action->unit->health,
                'max_health' => $this->action->unit->max_health,
            ];
        }

        if ($this->action->building) {
            $data['building'] = [
                'id' => $this->action->building->id,
                'type' => $this->action->building->type,
                'name' => $this->action->building->name,
                'level' => $this->action->building->level,
                'health' => $this->action->building->health,
            ];
        }

        if ($this->action->action_data) {
            $data['action_data'] = $this->action->action_data;
        }

        if (!$this->action->successful && $this->action->error_message) {
            $data['error_message'] = $this->action->error_message;
        }

        return $data;
    }
}
