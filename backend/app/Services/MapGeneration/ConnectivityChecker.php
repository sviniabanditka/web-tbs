<?php

namespace App\Services\MapGeneration;
/**
 * Checks the connectivity of the map and builds bridges between isolated areas
 */
class ConnectivityChecker
{
    /**
     * Main connectivity check for the map
     */
    public function checkConnectivity(array $map): array
    {
        $landAreas = $this->findDisconnectedAreas($map);

        if (count($landAreas) <= 1) {
            return $map; // The map is already connected
        }

        return $this->buildBridges($map, $landAreas);
    }

    /**
     * Find all separate land areas
     */
    private function findDisconnectedAreas(array $map): array
    {
        $visited = [];
        $areas = [];

        foreach ($map as $coordinateStr => $tile) {
            if (!$tile->isPassable() || isset($visited[$coordinateStr])) {
                continue;
            }

            $area = $this->floodFill($map, $tile->coordinate, $visited);
            if (!empty($area)) {
                $areas[] = $area;
            }
        }

        return $areas;
    }

    /**
     * Flood fill algorithm to find connected areas
     */
    private function floodFill(array $map, HexCoordinate $start, array &$visited): array
    {
        $area = [];
        $queue = [$start];
        $startKey = "{$start->q},{$start->r}";

        if (isset($visited[$startKey])) {
            return $area;
        }

        while (!empty($queue)) {
            $current = array_shift($queue);
            $key = "{$current->q},{$current->r}";

            if (isset($visited[$key])) {
                continue;
            }

            if (!isset($map[$key]) || !$map[$key]->isPassable()) {
                continue;
            }

            $visited[$key] = true;
            $area[] = $current;

            foreach ($current->getNeighbors() as $neighbor) {
                $neighborKey = "{$neighbor->q},{$neighbor->r}";
                if (!isset($visited[$neighborKey])) {
                    $queue[] = $neighbor;
                }
            }
        }

        return $area;
    }

    /**
     * Build bridges between isolated areas
     */
    private function buildBridges(array $map, array $landAreas): array
    {
        if (empty($landAreas)) {
            return $map;
        }

        $mainArea = $landAreas[0]; // The largest area

        // Connect all other areas to the main one
        for ($i = 1; $i < count($landAreas); $i++) {
            $bridgePath = $this->findShortestPath($mainArea, $landAreas[$i], $map);
            $map = $this->createBridge($map, $bridgePath);

            // Merge areas after creating the bridge
            $mainArea = array_merge($mainArea, $landAreas[$i]);
        }

        return $map;
    }

    /**
     * Find the shortest path between two areas
     */
    private function findShortestPath(array $area1, array $area2, array $map): array
    {
        $minDistance = PHP_INT_MAX;
        $bestPath = [];

        foreach ($area1 as $point1) {
            foreach ($area2 as $point2) {
                $distance = $point1->distance($point2);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $bestPath = $this->getLinePath($point1, $point2);
                }
            }
        }

        return $bestPath;
    }

    /**
     * Get a straight path between two points
     */
    private function getLinePath(HexCoordinate $start, HexCoordinate $end): array
    {
        $path = [];
        $distance = $start->distance($end);

        for ($i = 0; $i <= $distance; $i++) {
            $t = $distance == 0 ? 0 : $i / $distance;

            $q = $start->q + ($end->q - $start->q) * $t;
            $r = $start->r + ($end->r - $start->r) * $t;

            $path[] = new HexCoordinate((int)round($q), (int)round($r));
        }

        return $path;
    }

    /**
     * Create a bridge along the path
     */
    private function createBridge(array $map, array $path): array
    {
        foreach ($path as $coordinate) {
            $key = "{$coordinate->q},{$coordinate->r}";

            if (isset($map[$key]) && $map[$key]->biome === 'water') {
                $map[$key]->setBridge(true);
            }
        }

        return $map;
    }
}
