<?php

namespace App\Services\MapGen;

class StatisticGenerator
{
    /**
     * Calculate map statistics
     */
    public function calculateMapStatistics(array $map): array
    {
        $stats = [
            'biomes' => [],
            'resources' => [],
            'total_cells' => 0,
            'passable_cells' => 0,
            'bridges' => 0
        ];

        // Initialize counters
        $biomeCount = [];
        $resourceCount = [];
        $passableCount = 0;
        $bridgeCount = 0;

        // Calculate statistics for each tile
        foreach ($map as $tile) {
            // Total number of tiles
            $stats['total_cells']++;

            // Count biomes
            $biome = $tile->biome;
            if (!isset($biomeCount[$biome])) {
                $biomeCount[$biome] = 0;
            }
            $biomeCount[$biome]++;

            // Count resources
            if ($tile->resource !== null) {
                $resource = $tile->resource;
                if (!isset($resourceCount[$resource])) {
                    $resourceCount[$resource] = 0;
                }
                $resourceCount[$resource]++;
            }

            // Count passable tiles
            if ($tile->isPassable()) {
                $passableCount++;
            }

            // Count bridges
            if ($tile->isBridge) {
                $bridgeCount++;
            }
        }

        // Sort results for consistency
        ksort($biomeCount);
        ksort($resourceCount);

        // Fill in the final statistics
        $stats['biomes'] = $biomeCount;
        $stats['resources'] = $resourceCount;
        $stats['passable_cells'] = $passableCount;
        $stats['bridges'] = $bridgeCount;

        return $stats;
    }

    /**
     * Calculate biome percentages
     */
    public function calculateBiomePercentages(array $map): array
    {
        $stats = $this->calculateMapStatistics($map);
        $totalCells = $stats['total_cells'];

        $percentages = [];
        foreach ($stats['biomes'] as $biome => $count) {
            $percentages[$biome] = [
                'count' => $count,
                'percentage' => round(($count / $totalCells) * 100, 2)
            ];
        }

        return $percentages;
    }

    /**
     * Calculate resource density
     */
    public function calculateResourceDensity(array $map): array
    {
        $stats = $this->calculateMapStatistics($map);
        $totalCells = $stats['total_cells'];
        $totalResources = array_sum($stats['resources']);

        $density = [
            'total_resources' => $totalResources,
            'resource_density' => round(($totalResources / $totalCells) * 100, 2),
            'resources_breakdown' => []
        ];

        foreach ($stats['resources'] as $resource => $count) {
            $density['resources_breakdown'][$resource] = [
                'count' => $count,
                'percentage_of_total' => round(($count / $totalCells) * 100, 2),
                'percentage_of_resources' => round(($count / $totalResources) * 100, 2)
            ];
        }

        return $density;
    }

    /**
     * Full map statistics with additional metrics
     */
    public function getDetailedMapStatistics(array $map): array
    {
        $basicStats = $this->calculateMapStatistics($map);
        $biomePercentages = $this->calculateBiomePercentages($map);
        $resourceDensity = $this->calculateResourceDensity($map);

        return [
            'basic_stats' => $basicStats,
            'biome_analysis' => $biomePercentages,
            'resource_analysis' => $resourceDensity,
            'connectivity' => [
                'passable_percentage' => round(($basicStats['passable_cells'] / $basicStats['total_cells']) * 100, 2),
                'bridges_count' => $basicStats['bridges'],
                'water_tiles' => $basicStats['biomes']['water'] ?? 0
            ]
        ];
    }

    /**
     * Validate statistics against the configuration
     */
    public function validateMapAgainstConfig(array $map, array $config): array
    {
        $stats = $this->calculateMapStatistics($map);
        $totalCells = $stats['total_cells'];

        $validation = [
            'biomes' => [],
            'resources' => [],
            'overall_compliance' => true
        ];

        // Check biomes
        foreach ($config['biomes'] as $biome => $expectedCoeff) {
            $actualCount = $stats['biomes'][$biome] ?? 0;
            $actualPercentage = ($actualCount / $totalCells);
            $expectedPercentage = $expectedCoeff;

            $deviation = abs($actualPercentage - $expectedPercentage);
            $tolerance = 0.05; // 5% tolerance

            $validation['biomes'][$biome] = [
                'expected_percentage' => round($expectedPercentage * 100, 2),
                'actual_percentage' => round($actualPercentage * 100, 2),
                'deviation' => round($deviation * 100, 2),
                'within_tolerance' => $deviation <= $tolerance
            ];

            if ($deviation > $tolerance) {
                $validation['overall_compliance'] = false;
            }
        }

        // Check resources
        foreach ($config['resources']['limits'] as $resource => $limits) {
            $actualCount = $stats['resources'][$resource] ?? 0;
            $withinLimits = $actualCount >= $limits['min'] && $actualCount <= $limits['max'];

            $validation['resources'][$resource] = [
                'min_limit' => $limits['min'],
                'max_limit' => $limits['max'],
                'actual_count' => $actualCount,
                'within_limits' => $withinLimits
            ];

            if (!$withinLimits) {
                $validation['overall_compliance'] = false;
            }
        }

        return $validation;
    }
}
