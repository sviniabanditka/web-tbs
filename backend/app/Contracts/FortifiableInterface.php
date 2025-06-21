<?php

namespace App\Contracts;

interface FortifiableInterface
{
    /**
     * Check if the unit can fortify its position.
     *
     * @return bool
     */
    public function canFortify(): bool;
}
