<?php

namespace App\Services\MapGen;
/**
 * Represents the axial coordinates of a hexagon
 */
class HexCoordinate
{
    public int $q;
    public int $r;

    public function __construct(int $q, int $r)
    {
        $this->q = $q;
        $this->r = $r;
    }

    /**
     * Get neighboring coordinates (6 directions)
     */
    public function getNeighbors(): array
    {
        $directions = [
            [1, 0], [0, 1], [-1, 1],
            [-1, 0], [0, -1], [1, -1]
        ];

        $neighbors = [];
        foreach ($directions as [$dq, $dr]) {
            $neighbors[] = new HexCoordinate($this->q + $dq, $this->r + $dr);
        }

        return $neighbors;
    }

    /**
     * Calculate the distance to another point
     */
    public function distance(HexCoordinate $other): int
    {
        return (abs($this->q - $other->q) +
                abs($this->q + $this->r - $other->q - $other->r) +
                abs($this->r - $other->r)) / 2;
    }

    /**
     * Check if within radius
     */
    public function isInRadius(int $radius): bool
    {
        return $this->distance(new HexCoordinate(0, 0)) <= $radius;
    }

    public function __toString(): string
    {
        return "({$this->q},{$this->r})";
    }

    /**
     * Generate all coordinates within a radius
     */
    public static function generateInRadius(int $radius): array
    {
        $coordinates = [];
        for ($q = -$radius; $q <= $radius; $q++) {
            $r1 = max(-$radius, -$q - $radius);
            $r2 = min($radius, -$q + $radius);
            for ($r = $r1; $r <= $r2; $r++) {
                $coordinates[] = new HexCoordinate($q, $r);
            }
        }
        return $coordinates;
    }
}
