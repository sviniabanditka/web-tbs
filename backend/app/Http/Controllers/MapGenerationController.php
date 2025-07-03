<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\MapGen\MapGenerator;
use App\Services\MapGen\StatisticGenerator;

class MapGenerationController extends Controller
{

    public function generate(Request $request): JsonResponse
    {
        $config = [
            'biomes' => [
                'grass' => 0.30,
                'forest' => 0.15,
                'mountain' => 0.15,
                'tundra' => 0.10,
                'desert' => 0.10,
                'water' => 0.20,
            ],
            'resources' => [
                'rules' => [
                    'food' => ['grass', 'forest'],
                    'wood' => ['forest', 'tundra'],
                    'stone' => ['mountain'],
                    'iron' => ['mountain'],
                    'gold' => ['mountain'],
                ],
                'limits' => [
                    'food' => ['min' => 75, 'max' => 300],
                    'wood' => ['min' => 50, 'max' => 250],
                    'stone' => ['min' => 20, 'max' => 150],
                    'iron' => ['min' => 10, 'max' => 100],
                    'gold' => ['min' => 10, 'max' => 50],
                ],
                'coefficients' => [
                    'food' => 0.5,
                    'wood' => 0.4,
                    'stone' => 0.3,
                    'iron' => 0.2,
                    'gold' => 0.1,
                ],
            ],
        ];

        $seed = $request->input('seed');
        $radius = $request->input('radius');
        $biomeSize = $request->input('biome_size');

        $generator = new MapGenerator();
        $map = $generator->generate($seed, $radius, $config, $biomeSize);

        $statistic = new StatisticGenerator();
        $detailedStats = $statistic->getDetailedMapStatistics($map);
        $validatedStats = $statistic->validateMapAgainstConfig($map, $config);


        return response()->json([
            'map' => $map,
            'stats' => $detailedStats,
            'validation' => $validatedStats,
        ]);
    }
}
