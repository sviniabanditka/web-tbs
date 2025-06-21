<?php

namespace App\Contracts;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Http\Requests\ExecuteGameActionRequest;

interface GameActionInterface
{
    /**
     * Get the cost of the action in action points.
     *
     * @return int
     */
    public function getCost(): int;

    /**
     * Execute the game action.
     *
     * @param Game $game
     * @param GamePlayer $player
     * @param ExecuteGameActionRequest $request
     * @return array The result of the action.
     */
    public function execute(Game $game, GamePlayer $player, ExecuteGameActionRequest $request): array;
}
