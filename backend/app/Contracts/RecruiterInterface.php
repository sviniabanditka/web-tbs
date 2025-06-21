<?php

namespace App\Contracts;

use App\Enums\UnitType;

interface RecruiterInterface
{
    /**
     * Check if the entity can recruit a specific type of unit.
     *
     * @param UnitType $unitType
     * @return bool
     */
    public function canRecruit(UnitType $unitType): bool;
}
