<?php

namespace App\Events;

use App\Enums\GameStateChangeType;
use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameStateChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Game $game;
    public GameStateChangeType $changeType;
    public array $payload;

    /**
     * Create a new event instance.
     */
    public function __construct(Game $game, GameStateChangeType $changeType, array $payload = [])
    {
        $this->game = $game;
        $this->changeType = $changeType;
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('game.' . $this->game->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'game.state_changed';
    }

    public function broadcastWith(): array
    {
        $data = [
            'game_id' => $this->game->id,
            'change_type' => $this->changeType->value,
            'game_status' => $this->game->status,
            'current_turn' => $this->game->current_turn,
            'current_player_id' => $this->game->currentPlayer()?->id,
            'turn_ends_at' => $this->game->currentTurn?->ends_at?->toIso8601String(),
        ];

        // Add change-specific data
        switch ($this->changeType) {
            case GameStateChangeType::PLAYER_JOINED:
                $data['player'] = $this->payload['player'] ?? null;
                $data['players_count'] = $this->game->players()->count();
                break;

            case GameStateChangeType::PLAYER_LEFT:
                $data['player'] = $this->payload['player'] ?? null;
                $data['players_count'] = $this->game->players()->count();
                break;

            case GameStateChangeType::GAME_STARTED:
                $data['started_at'] = $this->game->started_at;
                $data['players'] = $this->game->players()->with('user')->get()->map(function ($player) {
                    return [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'name' => $player->user->name,
                        'color' => $player->color,
                        'faction' => $player->faction,
                        'turn_order' => $player->turn_order,
                    ];
                });
                break;

            case GameStateChangeType::GAME_FINISHED:
                $data['finished_at'] = $this->game->finished_at;
                $data['winner'] = $this->payload['winner'] ?? null;
                break;

            case GameStateChangeType::UNIT_MOVED:
                $data['unit'] = $this->payload['unit'] ?? null;
                $data['from_hex'] = $this->payload['from_hex'] ?? null;
                $data['to_hex'] = $this->payload['to_hex'] ?? null;
                break;

            case GameStateChangeType::UNIT_ATTACKED:
                $data['attacker'] = $this->payload['attacker'] ?? null;
                $data['defender'] = $this->payload['defender'] ?? null;
                $data['damage_dealt'] = $this->payload['damage_dealt'] ?? 0;
                $data['defender_destroyed'] = $this->payload['defender_destroyed'] ?? false;
                break;

            case GameStateChangeType::BUILDING_CONSTRUCTED:
                $data['building'] = $this->payload['building'] ?? null;
                $data['hex'] = $this->payload['hex'] ?? null;
                break;

            case GameStateChangeType::BUILDING_UPGRADED:
                $data['building'] = $this->payload['building'] ?? null;
                $data['old_level'] = $this->payload['old_level'] ?? null;
                $data['new_level'] = $this->payload['new_level'] ?? null;
                break;

            case GameStateChangeType::UNIT_RECRUITED:
                $data['unit'] = $this->payload['unit'] ?? null;
                $data['building'] = $this->payload['building'] ?? null;
                break;

            case GameStateChangeType::RESOURCES_UPDATED:
                $data['player'] = $this->payload['player'] ?? null;
                $data['resources'] = $this->payload['resources'] ?? null;
                break;
        }

        return $data;
    }
}
