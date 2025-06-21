<?php

namespace App\Contracts;

interface AttackableInterface
{
    /**
     * Check if the unit can perform an attack.
     *
     * @return bool
     */
    public function canAttack(): bool;

    /**
     * Get the combat strength of the unit (attack and defense).
     *
     * @return array{attack: int, defense: int}
     */
    public function getCombatStrength(): array;

    /**
     * Gain experience after a successful action (e.g., combat).
     *
     * @return void
     */
    public function gainExperience(): void;
}
