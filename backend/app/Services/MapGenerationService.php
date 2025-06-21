<?php

namespace App\Services;

use App\Models\Game;
use App\Models\GameHex;
use Illuminate\Support\Facades\DB;

class MapGenerationService
{
    private array $terrainTypes = [
        'grass' => ['elevation' => [0.3, 0.6], 'moisture' => [0.4, 0.8], 'temperature' => [0.3, 0.7]],
        'forest' => ['elevation' => [0.4, 0.7], 'moisture' => [0.6, 1.0], 'temperature' => [0.2, 0.6]],
        'mountain' => ['elevation' => [0.7, 1.0], 'moisture' => [0.2, 0.6], 'temperature' => [0.1, 0.5]],
        'water' => ['elevation' => [0.0, 0.3], 'moisture' => [0.8, 1.0], 'temperature' => [0.4, 0.8]],
        'desert' => ['elevation' => [0.2, 0.6], 'moisture' => [0.0, 0.3], 'temperature' => [0.7, 1.0]],
        'tundra' => ['elevation' => [0.3, 0.7], 'moisture' => [0.3, 0.7], 'temperature' => [0.0, 0.3]],
    ];

    private array $resourceTypes = [
        'iron' => ['terrain' => ['mountain', 'forest'], 'probability' => 0.15],
        'gold' => ['terrain' => ['mountain'], 'probability' => 0.08],
        'food' => ['terrain' => ['grass', 'forest'], 'probability' => 0.25],
        'wood' => ['terrain' => ['forest'], 'probability' => 0.30],
        'stone' => ['terrain' => ['mountain'], 'probability' => 0.20],
    ];

    public function generateMap(Game $game): void
    {
        $seed = $game->map_generation_seed;
        $algorithm = $game->map_generation_algorithm;
        $size = $game->map_size;
        $parameters = $game->terrain_parameters ?? [];

        // Set random seed for consistent generation
        mt_srand($seed);

        $hexes = [];
        $centerQ = 0;
        $centerR = 0;

        // Generate hexagonal grid
        for ($q = -$size; $q <= $size; $q++) {
            $r1 = max(-$size, -$q - $size);
            $r2 = min($size, -$q + $size);

            for ($r = $r1; $r <= $r2; $r++) {
                $hex = $this->generateHex($q, $r, $algorithm, $parameters, $seed);
                $hexes[] = $hex;
            }
        }

        // Batch insert hexes for efficiency
        DB::transaction(function () use ($game, $hexes) {
            foreach (array_chunk($hexes, 1000) as $chunk) {
                $game->hexes()->createMany($chunk);
            }
        });
    }

    private function generateHex(int $q, int $r, string $algorithm, array $parameters, int $seed): array
    {
        $hex = [
            'q' => $q,
            'r' => $r,
        ];

        switch ($algorithm) {
            case 'perlin':
                $hex = array_merge($hex, $this->generatePerlinHex($q, $r, $parameters, $seed));
                break;
            case 'voronoi':
                $hex = array_merge($hex, $this->generateVoronoiHex($q, $r, $parameters, $seed));
                break;
            case 'hybrid':
                $hex = array_merge($hex, $this->generateHybridHex($q, $r, $parameters, $seed));
                break;
            default:
                $hex = array_merge($hex, $this->generatePerlinHex($q, $r, $parameters, $seed));
        }

        return $hex;
    }

    private function generatePerlinHex(int $q, int $r, array $parameters, int $seed): array
    {
        $scale = $parameters['scale'] ?? 0.1;
        $octaves = $parameters['octaves'] ?? 4;
        $persistence = $parameters['persistence'] ?? 0.5;
        $lacunarity = $parameters['lacunarity'] ?? 2.0;

        // Convert axial to pixel coordinates for noise generation
        $x = $q * 3/2;
        $y = ($q * sqrt(3)/2) + ($r * sqrt(3));

        $elevation = $this->perlinNoise($x * $scale, $y * $scale, $octaves, $persistence, $lacunarity, $seed);
        $moisture = $this->perlinNoise($x * $scale * 1.5, $y * $scale * 1.5, $octaves, $persistence, $lacunarity, $seed + 1000);
        $temperature = $this->perlinNoise($x * $scale * 0.8, $y * $scale * 0.8, $octaves, $persistence, $lacunarity, $seed + 2000);

        // Normalize values to 0-1 range
        $elevation = ($elevation + 1) / 2;
        $moisture = ($moisture + 1) / 2;
        $temperature = ($temperature + 1) / 2;

        $terrainType = $this->determineTerrainType($elevation, $moisture, $temperature);
        $resourceData = $this->determineResource($terrainType, $elevation, $moisture, $temperature);

        return [
            'terrain_type' => $terrainType,
            'elevation' => $elevation,
            'moisture' => $moisture,
            'temperature' => $temperature,
            'resource_type' => $resourceData['type'],
            'resource_amount' => $resourceData['amount'],
            'is_passable' => $this->isPassable($terrainType),
            'movement_cost' => $this->getMovementCost($terrainType),
            'defense_bonus' => $this->getDefenseBonus($terrainType),
            'production_bonus' => $this->getProductionBonus($terrainType),
        ];
    }

    private function generateVoronoiHex(int $q, int $r, array $parameters, int $seed): array
    {
        $numPoints = $parameters['num_points'] ?? 20;
        $points = $this->generateVoronoiPoints($numPoints, $seed);

        // Find closest point
        $closestPoint = $this->findClosestPoint($q, $r, $points);
        $distance = $this->distanceToPoint($q, $r, $closestPoint);

        // Generate terrain based on point properties and distance
        $elevation = $closestPoint['elevation'] * (1 - $distance * 0.5);
        $moisture = $closestPoint['moisture'] * (1 - $distance * 0.3);
        $temperature = $closestPoint['temperature'] * (1 - $distance * 0.4);

        $terrainType = $this->determineTerrainType($elevation, $moisture, $temperature);
        $resourceData = $this->determineResource($terrainType, $elevation, $moisture, $temperature);

        return [
            'terrain_type' => $terrainType,
            'elevation' => $elevation,
            'moisture' => $moisture,
            'temperature' => $temperature,
            'resource_type' => $resourceData['type'],
            'resource_amount' => $resourceData['amount'],
            'is_passable' => $this->isPassable($terrainType),
            'movement_cost' => $this->getMovementCost($terrainType),
            'defense_bonus' => $this->getDefenseBonus($terrainType),
            'production_bonus' => $this->getProductionBonus($terrainType),
        ];
    }

    private function generateHybridHex(int $q, int $r, array $parameters, int $seed): array
    {
        // Combine Perlin and Voronoi generation
        $perlinWeight = $parameters['perlin_weight'] ?? 0.6;
        $voronoiWeight = 1 - $perlinWeight;

        $perlinHex = $this->generatePerlinHex($q, $r, $parameters, $seed);
        $voronoiHex = $this->generateVoronoiHex($q, $r, $parameters, $seed + 5000);

        return [
            'terrain_type' => $this->blendTerrainTypes($perlinHex['terrain_type'], $voronoiHex['terrain_type'], $perlinWeight),
            'elevation' => $perlinHex['elevation'] * $perlinWeight + $voronoiHex['elevation'] * $voronoiWeight,
            'moisture' => $perlinHex['moisture'] * $perlinWeight + $voronoiHex['moisture'] * $voronoiWeight,
            'temperature' => $perlinHex['temperature'] * $perlinWeight + $voronoiHex['temperature'] * $voronoiWeight,
            'resource_type' => $perlinHex['resource_type'],
            'resource_amount' => $perlinHex['resource_amount'],
            'is_passable' => $this->isPassable($perlinHex['terrain_type']),
            'movement_cost' => $this->getMovementCost($perlinHex['terrain_type']),
            'defense_bonus' => $this->getDefenseBonus($perlinHex['terrain_type']),
            'production_bonus' => $this->getProductionBonus($perlinHex['terrain_type']),
        ];
    }

    private function perlinNoise(float $x, float $y, int $octaves, float $persistence, float $lacunarity, int $seed): float
    {
        $total = 0;
        $frequency = 1;
        $amplitude = 1;
        $maxValue = 0;

        for ($i = 0; $i < $octaves; $i++) {
            $total += $this->interpolatedNoise($x * $frequency, $y * $frequency, $seed + $i) * $amplitude;
            $maxValue += $amplitude;
            $amplitude *= $persistence;
            $frequency *= $lacunarity;
        }

        return $total / $maxValue;
    }

    private function interpolatedNoise(float $x, float $y, int $seed): float
    {
        $intX = (int) $x;
        $intY = (int) $y;
        $fracX = $x - $intX;
        $fracY = $y - $intY;

        $v1 = $this->smoothNoise($intX, $intY, $seed);
        $v2 = $this->smoothNoise($intX + 1, $intY, $seed);
        $v3 = $this->smoothNoise($intX, $intY + 1, $seed);
        $v4 = $this->smoothNoise($intX + 1, $intY + 1, $seed);

        $i1 = $this->interpolate($v1, $v2, $fracX);
        $i2 = $this->interpolate($v3, $v4, $fracX);

        return $this->interpolate($i1, $i2, $fracY);
    }

    private function smoothNoise(int $x, int $y, int $seed): float
    {
        $corners = ($this->noise($x - 1, $y - 1, $seed) + $this->noise($x + 1, $y - 1, $seed) +
                   $this->noise($x - 1, $y + 1, $seed) + $this->noise($x + 1, $y + 1, $seed)) / 16;
        $sides = ($this->noise($x - 1, $y, $seed) + $this->noise($x + 1, $y, $seed) +
                 $this->noise($x, $y - 1, $seed) + $this->noise($x, $y + 1, $seed)) / 8;
        $center = $this->noise($x, $y, $seed) / 4;

        return $corners + $sides + $center;
    }

    private function noise(int $x, int $y, int $seed): float
    {
        $n = $x + $y * 57 + $seed;
        $n = ($n << 13) ^ $n;
        return (1.0 - (($n * ($n * $n * 15731 + 789221) + 1376312589) & 0x7fffffff) / 1073741824.0);
    }

    private function interpolate(float $a, float $b, float $blend): float
    {
        $theta = $blend * M_PI;
        $f = (1 - cos($theta)) * 0.5;
        return $a * (1 - $f) + $b * $f;
    }

    private function generateVoronoiPoints(int $numPoints, int $seed): array
    {
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $points[] = [
                'x' => (mt_rand() / mt_getrandmax()) * 2 - 1,
                'y' => (mt_rand() / mt_getrandmax()) * 2 - 1,
                'elevation' => (mt_rand() / mt_getrandmax()),
                'moisture' => (mt_rand() / mt_getrandmax()),
                'temperature' => (mt_rand() / mt_getrandmax()),
            ];
        }
        return $points;
    }

    private function findClosestPoint(int $q, int $r, array $points): array
    {
        $closest = $points[0];
        $minDistance = PHP_FLOAT_MAX;

        foreach ($points as $point) {
            $distance = $this->distanceToPoint($q, $r, $point);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closest = $point;
            }
        }

        return $closest;
    }

    private function distanceToPoint(int $q, int $r, array $point): float
    {
        $x = $q * 3/2;
        $y = ($q * sqrt(3)/2) + ($r * sqrt(3));

        return sqrt(pow($x - $point['x'], 2) + pow($y - $point['y'], 2));
    }

    private function determineTerrainType(float $elevation, float $moisture, float $temperature): string
    {
        if ($elevation < 0.3) {
            return 'water';
        } elseif ($elevation > 0.7) {
            return 'mountain';
        } elseif ($temperature < 0.3) {
            return 'tundra';
        } elseif ($temperature > 0.7 && $moisture < 0.3) {
            return 'desert';
        } elseif ($moisture > 0.6) {
            return 'forest';
        } else {
            return 'grass';
        }
    }

    private function determineResource(string $terrainType, float $elevation, float $moisture, float $temperature): array
    {
        foreach ($this->resourceTypes as $resourceType => $config) {
            if (in_array($terrainType, $config['terrain']) && (mt_rand() / mt_getrandmax()) < $config['probability']) {
                $amount = mt_rand(10, 50);
                return ['type' => $resourceType, 'amount' => $amount];
            }
        }

        return ['type' => null, 'amount' => 0];
    }

    private function blendTerrainTypes(string $type1, string $type2, float $weight): string
    {
        return (mt_rand() / mt_getrandmax()) < $weight ? $type1 : $type2;
    }

    private function isPassable(string $terrainType): bool
    {
        return $terrainType !== 'water';
    }

    private function getMovementCost(string $terrainType): int
    {
        $costs = [
            'grass' => 1,
            'forest' => 2,
            'mountain' => 3,
            'water' => 999, // Impassable
            'desert' => 2,
            'tundra' => 2,
        ];

        return $costs[$terrainType] ?? 1;
    }

    private function getDefenseBonus(string $terrainType): int
    {
        $bonuses = [
            'grass' => 0,
            'forest' => 1,
            'mountain' => 2,
            'water' => 0,
            'desert' => 0,
            'tundra' => 0,
        ];

        return $bonuses[$terrainType] ?? 0;
    }

    private function getProductionBonus(string $terrainType): int
    {
        $bonuses = [
            'grass' => 1,
            'forest' => 0,
            'mountain' => 0,
            'water' => 0,
            'desert' => 0,
            'tundra' => 0,
        ];

        return $bonuses[$terrainType] ?? 0;
    }
}
