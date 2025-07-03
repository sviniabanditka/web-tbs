<?php

namespace App\Services\MapGen;

class StatisticGenerator
{
    public function calculateMapStatistics(array $map): array
    {
        $stats = [
            'biomes' => [],
            'resources' => [],
            'total_cells' => 0,
            'passable_cells' => 0,
            'bridges' => 0
        ];

        $biomeCount = [];
        $resourceCount = [];
        $passableCount = 0;
        $bridgeCount = 0;

        foreach ($map as $tile) {
            $stats['total_cells']++;

            // Подсчет биомов
            $biome = $tile->biome;
            if (!isset($biomeCount[$biome])) {
                $biomeCount[$biome] = 0;
            }
            $biomeCount[$biome]++;

            // Подсчет ресурсов
            if ($tile->resource !== null) {
                $resource = $tile->resource;
                if (!isset($resourceCount[$resource])) {
                    $resourceCount[$resource] = 0;
                }
                $resourceCount[$resource]++;
            }

            if ($tile->isPassable()) {
                $passableCount++;
            }

            if ($tile->isBridge) {
                $bridgeCount++;
            }
        }

        ksort($biomeCount);
        ksort($resourceCount);

        $stats['biomes'] = $biomeCount;
        $stats['resources'] = $resourceCount;
        $stats['passable_cells'] = $passableCount;
        $stats['bridges'] = $bridgeCount;

        return $stats;
    }

    /**
     * Анализ соответствия конфигурации ресурсов
     */
    public function analyzeResourceConfiguration(array $map, array $config): array
    {
        $stats = $this->calculateMapStatistics($map);
        $totalCells = $stats['total_cells'];

        $analysis = [
            'resource_distribution' => [],
            'abundance_analysis' => [],
            'biome_availability' => [],
            'overall_efficiency' => 0
        ];

        $resourceConfig = $config['resources'];
        $abundance = $resourceConfig['abundance'] ?? 0.5;
        $scarcitySettings = $resourceConfig['scarcity'] ?? [];
        $rules = $resourceConfig['rules'] ?? [];

        // Анализ каждого ресурса
        foreach ($rules as $resource => $allowedBiomes) {
            $actualCount = $stats['resources'][$resource] ?? 0;
            $scarcity = $scarcitySettings[$resource] ?? 0.5;

            // Подсчет доступных тайлов для этого ресурса
            $availableTiles = $this->countAvailableTilesForResource($map, $allowedBiomes);

            // Ожидаемое количество на основе формулы
            $expectedDensity = $abundance * 0.3 * $scarcity;
            $expectedCount = (int)round($availableTiles * $expectedDensity);

            $efficiency = $availableTiles > 0 ? ($actualCount / $availableTiles) : 0;

            $analysis['resource_distribution'][$resource] = [
                'actual_count' => $actualCount,
                'expected_count' => $expectedCount,
                'available_tiles' => $availableTiles,
                'efficiency' => round($efficiency * 100, 2),
                'scarcity_setting' => $scarcity,
                'density_achieved' => round(($actualCount / $totalCells) * 100, 2)
            ];
        }

        // Общий анализ abundance
        $totalResourcesPlaced = array_sum($stats['resources']);
        $totalResourcesExpected = $this->calculateExpectedTotalResources($map, $resourceConfig);

        $analysis['abundance_analysis'] = [
            'setting' => $abundance,
            'total_resources_placed' => $totalResourcesPlaced,
            'total_resources_expected' => $totalResourcesExpected,
            'abundance_efficiency' => $totalResourcesExpected > 0 ?
                round(($totalResourcesPlaced / $totalResourcesExpected) * 100, 2) : 0,
            'overall_density' => round(($totalResourcesPlaced / $totalCells) * 100, 2)
        ];

        // Анализ доступности биомов
        foreach ($rules as $resource => $allowedBiomes) {
            $biomeStats = [];
            foreach ($allowedBiomes as $biome) {
                $biomeCount = $stats['biomes'][$biome] ?? 0;
                $biomeStats[$biome] = $biomeCount;
            }

            $analysis['biome_availability'][$resource] = [
                'allowed_biomes' => $allowedBiomes,
                'biome_counts' => $biomeStats,
                'total_suitable_tiles' => array_sum($biomeStats)
            ];
        }

        // Общая эффективность конфигурации
        $efficiencyScores = array_column($analysis['resource_distribution'], 'efficiency');
        $analysis['overall_efficiency'] = !empty($efficiencyScores) ?
            round(array_sum($efficiencyScores) / count($efficiencyScores), 2) : 0;

        return $analysis;
    }

    /**
     * Подсчет ожидаемого общего количества ресурсов
     */
    private function calculateExpectedTotalResources(array $map, array $resourceConfig): int
    {
        $abundance = $resourceConfig['abundance'] ?? 0.5;
        $scarcitySettings = $resourceConfig['scarcity'] ?? [];
        $rules = $resourceConfig['rules'] ?? [];

        $totalExpected = 0;

        foreach ($rules as $resource => $allowedBiomes) {
            $availableTiles = $this->countAvailableTilesForResource($map, $allowedBiomes);
            $scarcity = $scarcitySettings[$resource] ?? 0.5;

            $expectedDensity = $abundance * 0.3 * $scarcity;
            $expectedCount = (int)round($availableTiles * $expectedDensity);

            $totalExpected += $expectedCount;
        }

        return $totalExpected;
    }

    /**
     * Подсчет доступных тайлов для конкретного ресурса
     */
    private function countAvailableTilesForResource(array $map, array $allowedBiomes): int
    {
        $count = 0;

        foreach ($map as $tile) {
            if (in_array($tile->biome, $allowedBiomes) && $tile->isPassable()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Новая валидация конфигурации ресурсов
     */
    public function validateResourceConfiguration(array $config): array
    {
        $validation = [
            'valid' => true,
            'errors' => [],
            'warnings' => []
        ];

        $resourceConfig = $config['resources'] ?? [];

        // Проверка abundance
        if (!isset($resourceConfig['abundance'])) {
            $validation['errors'][] = 'Missing abundance parameter';
            $validation['valid'] = false;
        } else {
            $abundance = $resourceConfig['abundance'];
            if (!is_numeric($abundance) || $abundance < 0.1 || $abundance > 1.0) {
                $validation['errors'][] = 'Abundance must be between 0.1 and 1.0';
                $validation['valid'] = false;
            }
        }

        // Проверка scarcity
        if (!isset($resourceConfig['scarcity']) || !is_array($resourceConfig['scarcity'])) {
            $validation['errors'][] = 'Missing or invalid scarcity configuration';
            $validation['valid'] = false;
        } else {
            foreach ($resourceConfig['scarcity'] as $resource => $scarcity) {
                if (!is_numeric($scarcity) || $scarcity < 0.1 || $scarcity > 1.0) {
                    $validation['errors'][] = "Scarcity for '$resource' must be between 0.1 and 1.0";
                    $validation['valid'] = false;
                }
            }
        }

        // Проверка rules
        if (!isset($resourceConfig['rules']) || !is_array($resourceConfig['rules'])) {
            $validation['errors'][] = 'Missing or invalid rules configuration';
            $validation['valid'] = false;
        } else {
            $validBiomes = ['grass', 'forest', 'mountain', 'tundra', 'desert', 'water'];

            foreach ($resourceConfig['rules'] as $resource => $allowedBiomes) {
                if (!is_array($allowedBiomes) || empty($allowedBiomes)) {
                    $validation['errors'][] = "Rules for '$resource' must be a non-empty array";
                    $validation['valid'] = false;
                }

                foreach ($allowedBiomes as $biome) {
                    if (!in_array($biome, $validBiomes)) {
                        $validation['errors'][] = "Invalid biome '$biome' for resource '$resource'";
                        $validation['valid'] = false;
                    }
                }
            }
        }

        // Проверка соответствия между scarcity и rules
        $scarcityResources = array_keys($resourceConfig['scarcity'] ?? []);
        $rulesResources = array_keys($resourceConfig['rules'] ?? []);

        $missingInRules = array_diff($scarcityResources, $rulesResources);
        $missingInScarcity = array_diff($rulesResources, $scarcityResources);

        if (!empty($missingInRules)) {
            $validation['warnings'][] = 'Resources in scarcity but not in rules: ' . implode(', ', $missingInRules);
        }

        if (!empty($missingInScarcity)) {
            $validation['warnings'][] = 'Resources in rules but not in scarcity: ' . implode(', ', $missingInScarcity);
        }

        return $validation;
    }

    /**
     * Рекомендации по улучшению конфигурации ресурсов
     */
    public function getResourceConfigurationRecommendations(array $map, array $config): array
    {
        $analysis = $this->analyzeResourceConfiguration($map, $config);
        $recommendations = [];

        // Рекомендации по abundance
        $abundanceEfficiency = $analysis['abundance_analysis']['abundance_efficiency'];

        if ($abundanceEfficiency < 70) {
            $recommendations[] = [
                'type' => 'abundance',
                'message' => 'Consider increasing abundance parameter for more resources on map',
                'current_value' => $config['resources']['abundance'],
                'suggested_value' => min(1.0, $config['resources']['abundance'] + 0.2)
            ];
        } elseif ($abundanceEfficiency > 130) {
            $recommendations[] = [
                'type' => 'abundance',
                'message' => 'Consider decreasing abundance parameter - too many resources',
                'current_value' => $config['resources']['abundance'],
                'suggested_value' => max(0.1, $config['resources']['abundance'] - 0.2)
            ];
        }

        // Рекомендации по scarcity отдельных ресурсов
        foreach ($analysis['resource_distribution'] as $resource => $data) {
            if ($data['efficiency'] < 50) {
                $recommendations[] = [
                    'type' => 'scarcity',
                    'resource' => $resource,
                    'message' => "Resource '$resource' appears too rarely - consider increasing scarcity",
                    'current_value' => $data['scarcity_setting'],
                    'suggested_value' => min(1.0, $data['scarcity_setting'] + 0.2)
                ];
            }

            if ($data['available_tiles'] < 5) {
                $recommendations[] = [
                    'type' => 'biome_availability',
                    'resource' => $resource,
                    'message' => "Resource '$resource' has very few suitable tiles - consider adding more allowed biomes",
                    'available_tiles' => $data['available_tiles']
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Полный отчет о карте и конфигурации
     */
    public function generateMapReport(array $map, array $config): array
    {
        return [
            'basic_statistics' => $this->calculateMapStatistics($map),
            'biome_analysis' => $this->calculateBiomePercentages($map),
            'resource_analysis' => $this->analyzeResourceConfiguration($map, $config),
            'config_validation' => $this->validateResourceConfiguration($config),
            'recommendations' => $this->getResourceConfigurationRecommendations($map, $config),
            'generation_summary' => [
                'total_cells' => count($map),
                'biome_size_impact' => 'Not tracked in this version',
                'connectivity_status' => 'Connected via bridges: ' . $this->calculateMapStatistics($map)['bridges']
            ]
        ];
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
