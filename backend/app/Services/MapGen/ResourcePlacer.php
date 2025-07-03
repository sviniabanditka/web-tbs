<?php

namespace App\Services\MapGen;
/**
 * Places resources on the map according to rules and limits
 */
class ResourcePlacer
{
    private array $rules;
    private array $limits;
    private array $coefficients;

    public function __construct(array $resourceConfig)
    {
        $this->rules = $resourceConfig['rules'];
        $this->limits = $resourceConfig['limits'];
        $this->coefficients = $resourceConfig['coefficients'];
    }

    /**
     * Placement of all resources on the map
     */
    public function placeResources(array $map, string $seedString): array
    {
        $resourceSeed = crc32($seedString . '_resources');
        mt_srand($resourceSeed);

        foreach ($this->rules as $resource => $allowedBiomes) {
            $count = $this->calculateResourceCount($resource);
            $map = $this->distributeResource($map, $resource, $allowedBiomes, $count);
        }

        return $map;
    }

    /**
     * Calculation of the number of resources to place
     */
    private function calculateResourceCount(string $resource): int
    {
        $min = $this->limits[$resource]['min'];
        $max = $this->limits[$resource]['max'];
        $coefficient = $this->coefficients[$resource];

        $range = $max - $min;
        $baseCount = $min + ($range * $coefficient);

        // Add a small randomness (±10%)
        $randomFactor = 0.9 + (mt_rand() / mt_getrandmax()) * 0.2;

        return (int)round($baseCount * $randomFactor);
    }

    /**
     * Placement of a specific resource
     */
    private function distributeResource(array $map, string $resource, array $allowedBiomes, int $count): array
    {
        $validPositions = $this->findValidPositions($map, $allowedBiomes);

        if (empty($validPositions)) {
            return $map;
        }

        // Deterministic shuffling of positions
        $this->deterministicShuffle($validPositions);

        $placedCount = 0;
        foreach ($validPositions as $position) {
            if ($placedCount >= $count) {
                break;
            }

            $key = "{$position->q},{$position->r}";
            if (isset($map[$key]) && $map[$key]->resource === null) {
                $map[$key]->setResource($resource);
                $placedCount++;
            }
        }

        return $map;
    }

    /**
     * Search for valid positions for the resource
     */
    private function findValidPositions(array $map, array $allowedBiomes): array
    {
        $validPositions = [];

        foreach ($map as $tile) {
            if (in_array($tile->biome, $allowedBiomes) &&
                $tile->isPassable() &&
                $tile->resource === null) {
                $validPositions[] = $tile->coordinate;
            }
        }

        return $validPositions;
    }

    /**
     * Deterministic shuffling of an array
     */
    private function deterministicShuffle(array &$array): void
    {
        $size = count($array);
        for ($i = $size - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$array[$i], $array[$j]] = [$array[$j], $array[$i]];
        }
    }
}
