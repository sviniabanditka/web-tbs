<?php

namespace App\Events;

use App\Models\Game;
use App\Models\GamePlayer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameTurnStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $activePlayer;
    public $turnNumber;
    public $timeRemaining;

    public function __construct(Game $game, GamePlayer $activePlayer, int $turnNumber, int $timeRemaining)
    {
        $this->game = $game;
        $this->activePlayer = $activePlayer;
        $this->turnNumber = $turnNumber;
        $this->timeRemaining = $timeRemaining;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("game.{$this->game->id}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'turn.started';
    }

    public function broadcastWith(): array
    {
        return [
            'game_id' => $this->game->id,
            'turn_number' => $this->turnNumber,
            'active_player' => [
                'id' => $this->activePlayer->id,
                'user_id' => $this->activePlayer->user_id,
                'username' => $this->activePlayer->user->name,
                'color' => $this->activePlayer->color,
                'faction' => $this->activePlayer->faction,
                'action_points_remaining' => $this->game->getRemainingActionPoints($this->activePlayer),
            ],
            'time_remaining' => $this->timeRemaining,
            'action_points_per_turn' => $this->game->action_points_per_turn,
        ];
    }
}
