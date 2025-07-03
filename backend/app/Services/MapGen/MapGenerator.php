<?php
namespace App\Services\MapGen;
/**
 * Main orchestrator for hexagonal map generation
 */
class MapGenerator
{
    private NoiseGenerator $noiseGenerator;
    private BiomeAssigner $biomeAssigner;
    private ConnectivityChecker $connectivityChecker;
    private ResourcePlacer $resourcePlacer;

    /**
     * Main map generation method
     */
    public function generate(string $seed, int $radius, array $config, float $biomeSizeCoefficient = 0.5): array
    {
        // Initialize components
        $this->noiseGenerator = new NoiseGenerator($seed);
        $this->biomeAssigner = new BiomeAssigner($config['biomes'], $biomeSizeCoefficient);
        $this->connectivityChecker = new ConnectivityChecker();
        $this->resourcePlacer = new ResourcePlacer($config['resources']);

        // The rest of the logic remains the same...
        $noiseMap = $this->generateNoise($seed, $radius);
        $biomeMap = $this->biomeAssigner->assignBiomes($noiseMap, $seed);
        $map = $this->createTileMap($radius, $biomeMap);
        $map = $this->connectivityChecker->checkConnectivity($map);
        $map = $this->resourcePlacer->placeResources($map, $seed);

        return $map;
    }

    /**
     * Generate noise for all coordinates
     */
    private function generateNoise(string $seed, int $radius): array
    {
        $coordinates = HexCoordinate::generateInRadius($radius);
        $noiseMap = [];

        foreach ($coordinates as $coordinate) {
            $key = "{$coordinate->q},{$coordinate->r}";
            // Use layered noise
            $noiseMap[$key] = $this->noiseGenerator->generateLayeredNoise($coordinate->q, $coordinate->r);
        }

        return $noiseMap;
    }

    /**
     * Create the tile map
     */
    private function createTileMap(int $radius, array $biomeMap): array
    {
        $coordinates = HexCoordinate::generateInRadius($radius);
        $map = [];

        foreach ($coordinates as $coordinate) {
            $key = "{$coordinate->q},{$coordinate->r}";
            $tile = new HexTile($coordinate);

            if (isset($biomeMap[$key])) {
                $tile->setBiome($biomeMap[$key]);
            }

            $map[$key] = $tile;
        }

        return $map;
    }
}
