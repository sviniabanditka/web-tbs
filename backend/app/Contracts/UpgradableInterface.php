<?php

namespace App\Contracts;

interface UpgradableInterface
{
    /**
     * Check if the entity can be upgraded.
     *
     * @return bool
     */
    public function canUpgrade(): bool;
}
