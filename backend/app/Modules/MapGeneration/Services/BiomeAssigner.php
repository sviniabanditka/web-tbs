<?php

namespace App\Modules\MapGeneration\Services;

class BiomeAssigner
{
    private array $biomeCoefficients;
    private float $biomeSizeCoefficient;

    public function __construct(array $biomeCoefficients, float $biomeSizeCoefficient = 0.5)
    {
        $this->biomeCoefficients = $biomeCoefficients;
        $this->biomeSizeCoefficient = max(0.1, min(1.0, $biomeSizeCoefficient));
    }

    /**
     * Main method for assigning biomes
     */
    public function assignBiomes(array $noiseMap, string $seedString): array
    {
        $coordinates = array_keys($noiseMap);
        $totalTiles = count($coordinates);

        // Create seed points taking biome size into account
        $seedPoints = $this->generateSeedPoints($coordinates, $seedString, $totalTiles);

        // Initialize the map
        $biomeMap = $this->initializeBiomeMap($coordinates, $seedPoints);

        // Expand biomes with size control
        $biomeMap = $this->expandBiomes($biomeMap, $noiseMap, $coordinates);

        // Smoothing with size consideration
        return $this->smoothBiomes($biomeMap);
    }

    /**
     * Generate seed points for each biome
     */
    private function generateSeedPoints(array $coordinates, string $seedString, int $totalTiles): array
    {
        $seed = crc32($seedString . '_seeds');
        mt_srand($seed);

        // Calculate seed point density based on size coefficient
        // At 0.1 - many small biomes (more seed points)
        // At 1.0 - few large biomes (fewer seed points)
        $seedDensity = $this->calculateSeedDensity($totalTiles);

        $seedPoints = [];
        $shuffledCoords = $coordinates;
        $this->deterministicShuffle($shuffledCoords, $seed);

        $coordIndex = 0;

        foreach ($this->biomeCoefficients as $biome => $coefficient) {
            // Number of seed points for each biome
            $baseSeedCount = (int)round($totalTiles * $coefficient * $seedDensity);
            $seedCount = max(1, $baseSeedCount); // At least 1 seed

            for ($i = 0; $i < $seedCount; $i++) {
                if ($coordIndex < count($shuffledCoords)) {
                    $seedPoints[$shuffledCoords[$coordIndex]] = $biome;
                    $coordIndex++;
                }
            }
        }

        return $seedPoints;
    }

    /**
     * Calculate seed point density
     */
    private function calculateSeedDensity(int $totalTiles): float
    {
        // Formula for converting size coefficient to seed point density
        // Smaller coefficient = more seeds = smaller biomes

        $minDensity = 0.02;  // 2% minimum
        $maxDensity = 0.15;  // 15% maximum

        // Inverted exponential function
        $normalizedSize = 1.0 - $this->biomeSizeCoefficient; // invert
        $density = $minDensity + ($maxDensity - $minDensity) * pow($normalizedSize, 1.5);

        return $density;
    }

    /**
     * Biome expansion with aggression control
     */
    private function expandBiomes(array $biomeMap, array $noiseMap, array $coordinates): array
    {
        $maxIterations = $this->calculateMaxIterations();
        $expansionRate = $this->calculateExpansionRate();

        $iteration = 0;

        while ($iteration < $maxIterations) {
            $hasChanges = false;
            $newMap = $biomeMap;

            // Sort coordinates for deterministic processing order
            $shuffledCoords = array_keys($biomeMap);
            $this->deterministicShuffle($shuffledCoords, crc32('expand_' . $iteration));

            foreach ($shuffledCoords as $coordStr) {
                if ($biomeMap[$coordStr] !== null) continue;

                [$q, $r] = explode(',', $coordStr);
                $coord = new HexCoordinate((int)$q, (int)$r);

                $neighborBiomes = $this->getNeighborBiomes($coord, $biomeMap);

                if (!empty($neighborBiomes)) {
                    // The probability of expansion depends on the size of the biomes
                    if ($this->shouldExpand($expansionRate, $coordStr, $iteration)) {
                        $selectedBiome = $this->selectBiomeFromNeighbors(
                            $neighborBiomes,
                            $noiseMap[$coordStr],
                            $coordStr
                        );

                        if ($selectedBiome) {
                            $newMap[$coordStr] = $selectedBiome;
                            $hasChanges = true;
                        }
                    }
                }
            }

            $biomeMap = $newMap;
            $iteration++;

            if (!$hasChanges) break;
        }

        // Fill in the remaining unassigned tiles
        foreach ($biomeMap as $coordStr => $biome) {
            if ($biome === null) {
                $biomeMap[$coordStr] = $this->getRandomBiome($coordStr);
            }
        }

        return $biomeMap;
    }

    /**
     * Calculate the maximum number of iterations
     */
    private function calculateMaxIterations(): int
    {
        // More iterations for larger biomes
        $baseIterations = 50;
        $multiplier = 0.5 + ($this->biomeSizeCoefficient * 1.5);

        return (int)round($baseIterations * $multiplier);
    }

    /**
     * Calculate the expansion rate
     */
    private function calculateExpansionRate(): float
    {
        // Larger size coefficient = more aggressive expansion
        return 0.3 + ($this->biomeSizeCoefficient * 0.6); // from 0.3 to 0.9
    }

    /**
     * Decision to expand a biome
     */
    private function shouldExpand(float $expansionRate, string $coordStr, int $iteration): bool
    {
        $hash = crc32($coordStr . '_expand_' . $iteration);
        $random = ($hash & 0x7FFFFFFF) / 0x7FFFFFFF;

        // Decrease probability over time
        $iterationFactor = max(0.1, 1.0 - ($iteration * 0.02));
        $adjustedRate = $expansionRate * $iterationFactor;

        return $random < $adjustedRate;
    }

    /**
     * Select a biome considering cluster size
     */
    private function selectBiomeFromNeighbors(array $neighborBiomes, float $noise, string $coordStr): ?string
    {
        if (empty($neighborBiomes)) return null;

        $biomeCounts = array_count_values($neighborBiomes);
        $weights = [];

        foreach ($biomeCounts as $biome => $count) {
            $baseWeight = $count / count($neighborBiomes);

            // Influence of biome size on cohesion
            $cohesionBonus = $this->biomeSizeCoefficient * 0.5; // cohesion bonus
            $noiseModifier = 1.0 + ($noise * (0.3 - $cohesionBonus)); // less noise for larger biomes

            $weights[$biome] = $baseWeight * $noiseModifier * (1.0 + $cohesionBonus);
        }

        return $this->weightedRandomSelect($weights, $coordStr);
    }

    /**
     * Smoothing with biome size consideration
     */
    private function smoothBiomes(array $biomeMap): array
    {
        $smoothed = $biomeMap;

        // Number of smoothing iterations depends on biome size
        $smoothingIterations = (int)round(1 + ($this->biomeSizeCoefficient * 2)); // 1-3 iterations

        for ($iteration = 0; $iteration < $smoothingIterations; $iteration++) {
            $newMap = $smoothed;

            foreach ($biomeMap as $coordStr => $biome) {
                [$q, $r] = explode(',', $coordStr);
                $coord = new HexCoordinate((int)$q, (int)$r);

                $neighborBiomes = $this->getNeighborBiomes($coord, $smoothed);

                if (count($neighborBiomes) >= 3) {
                    $biomeCounts = array_count_values($neighborBiomes);
                    arsort($biomeCounts);

                    $mostCommon = array_key_first($biomeCounts);

                    if ($biomeCounts[$mostCommon] >= 3 && $mostCommon !== $biome) {
                        // Smoothing probability depends on biome size
                        $smoothingChance = 0.1 + ($this->biomeSizeCoefficient * 0.3); // 10%-40%

                        $hash = crc32($coordStr . '_smooth_' . $iteration);
                        $random = ($hash & 0x7FFFFFFF) / 0x7FFFFFFF;

                        if ($random < $smoothingChance) {
                            $newMap[$coordStr] = $mostCommon;
                        }
                    }
                }
            }

            $smoothed = $newMap;
        }

        return $smoothed;
    }

    /**
     * Initialize the biome map with seed points
     */
    private function initializeBiomeMap(array $coordinates, array $seedPoints): array
    {
        $biomeMap = [];

        // Initialize all as unassigned
        foreach ($coordinates as $coord) {
            $biomeMap[$coord] = null;
        }

        // Assign seed points
        foreach ($seedPoints as $coord => $biome) {
            $biomeMap[$coord] = $biome;
        }

        return $biomeMap;
    }

    /**
     * Get the biomes of neighboring tiles
     */
    private function getNeighborBiomes(HexCoordinate $coord, array $biomeMap): array
    {
        $neighborBiomes = [];

        foreach ($coord->getNeighbors() as $neighbor) {
            $neighborKey = "{$neighbor->q},{$neighbor->r}";

            if (isset($biomeMap[$neighborKey]) && $biomeMap[$neighborKey] !== null) {
                $neighborBiomes[] = $biomeMap[$neighborKey];
            }
        }

        return $neighborBiomes;
    }

    /**
     * Weighted random selection
     */
    private function weightedRandomSelect(array $weights, string $coordStr): string
    {
        $totalWeight = array_sum($weights);

        // Deterministic "randomness" based on coordinates
        $hash = crc32($coordStr . '_select');
        $random = ($hash & 0x7FFFFFFF) / 0x7FFFFFFF; // Normalize to [0,1]

        $threshold = $random * $totalWeight;
        $current = 0;

        foreach ($weights as $biome => $weight) {
            $current += $weight;
            if ($current >= $threshold) {
                return $biome;
            }
        }

        // Fallback
        return array_key_first($weights);
    }

    /**
     * Get a random biome for unassigned tiles
     */
    private function getRandomBiome(string $coordStr): string
    {
        $hash = crc32($coordStr . '_fallback');
        $random = ($hash & 0x7FFFFFFF) / 0x7FFFFFFF;

        $totalWeight = array_sum($this->biomeCoefficients);
        $threshold = $random * $totalWeight;
        $current = 0;

        foreach ($this->biomeCoefficients as $biome => $weight) {
            $current += $weight;
            if ($current >= $threshold) {
                return $biome;
            }
        }

        return array_key_first($this->biomeCoefficients);
    }

    /**
     * Deterministic shuffling
     */
    private function deterministicShuffle(array &$array, int $seed): void
    {
        mt_srand($seed);
        $size = count($array);

        for ($i = $size - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$array[$i], $array[$j]] = [$array[$j], $array[$i]];
        }
    }
}
