<?php

namespace App\Modules\MapGeneration\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Modules\Common\Controllers\Controller;
use App\Modules\MapGeneration\Services\MapGenerator;
use App\Modules\MapGeneration\Services\StatisticGenerator;

class MapGenerationController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $seed = $request->input('seed', Str::random(10));
        $radius = $request->input('radius', 25);
        $biomeSize = $request->input('biome_size', 0.5);
        $abundance = $request->input('abundance', 0.8);
        $scarcity = $request->input('scarcity', []);
        $biomes = $request->input('biomes', []);
        $config = [
            'biomes' => [
                'grass' => $biomes['grass'] ?? 0.30,
                'forest' => $biomes['forest'] ?? 0.15,
                'mountain' => $biomes['mountain'] ?? 0.15,
                'tundra' => $biomes['tundra'] ?? 0.10,
                'desert' => $biomes['desert'] ?? 0.10,
                'water' => $biomes['water'] ?? 0.20,
            ],
            'resources' => [
                'abundance' => $abundance,
                'scarcity' => [
                    'food' => $scarcity['food'] ?? 0.8,
                    'wood' => $scarcity['wood'] ?? 0.8,
                    'stone' => $scarcity['stone'] ?? 0.7,
                    'iron' => $scarcity['iron'] ?? 0.5,
                    'gold' => $scarcity['gold'] ?? 0.3,
                ],
                'rules' => [
                    'food' => ['grass', 'forest'],
                    'wood' => ['forest', 'tundra'],
                    'stone' => ['mountain'],
                    'iron' => ['mountain'],
                    'gold' => ['mountain'],
                ],
            ],
        ];

        $generator = new MapGenerator();
        $map = $generator->generate($seed, $radius, $config, $biomeSize);

        $statistic = new StatisticGenerator();
        $stats = $statistic->generateMapReport($map, $config);

        return response()->json([
            'map' => $map,
            'stats' => $stats,
        ]);
    }
}
