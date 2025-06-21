<?php

namespace App\Contracts;

use App\Models\GameHex;

interface MovableInterface
{
    /**
     * Check if the unit can reach a specific hex.
     *
     * @param GameHex $hex
     * @return bool
     */
    public function canReachHex(GameHex $hex): bool;

    /**
     * Get the movement cost to a specific hex.
     *
     * @param GameHex $hex
     * @return int
     */
    public function getMovementCost(GameHex $hex): int;

    /**
     * Reset the unit's movement points at the start of a turn.
     *
     * @return void
     */
    public function resetMovementPoints(): void;
}
