<?php

namespace App\Modules\MapGeneration\Services;

class ResourcePlacer
{
    private float $abundance;
    private array $scarcity;
    private array $rules;
    private int $resourceSeed;

    /**
     * @param array $resourceConfig
     *   [
     *     'abundance' => float,       // от 0.1 до 1.0
     *     'scarcity' => [             // 0.1 = очень редкий, 1.0 = частый
     *       'food'  => float,
     *       'wood'  => float,
     *       'stone' => float,
     *       'iron'  => float,
     *       'gold'  => float,
     *     ],
     *     'rules' => [                // в каких биомах может появляться ресурс
     *       'food'  => ['grass','forest'],
     *       'wood'  => ['forest','tundra'],
     *       'stone' => ['mountain'],
     *       'iron'  => ['mountain'],
     *       'gold'  => ['mountain'],
     *     ]
     *   ]
     */
    public function __construct(array $resourceConfig)
    {
        $this->abundance = $resourceConfig['abundance'] ?? 0.5;
        $this->scarcity = $resourceConfig['scarcity'] ?? [];
        $this->rules    = $resourceConfig['rules']   ?? [];
    }

    /**
     * Размещает ресурсы детерминированно на основе seed и конфига.
     *
     * @param array  $map        Ассоциативный массив HexTile, ключ = "q,r"
     * @param string $seedString Строковый seed
     * @return array Модифицированный $map с ресурсами
     */
    public function placeResources(array $map, string $seedString): array
    {
        // Генерируем единый seed для всех ресурсов
        $this->resourceSeed = crc32($seedString . '_resources');

        foreach ($this->rules as $resource => $allowedBiomes) {
            // Подсчитать, сколько тайлов подходит под этот ресурс
            $validTiles = $this->countValidTiles($map, $allowedBiomes);

            // Вычислить точное количество по формуле: validTiles × abundance × scarcity
            $count = $this->calculateResourceCount($resource, $validTiles);

            // Разместить ровно $count ресурсов на случайные, но детерминированные позиции
            $map = $this->distributeResource($map, $resource, $allowedBiomes, $count);
        }

        return $map;
    }

    /**
     * Считает число тайлов, подходящих для данного ресурса.
     */
    private function countValidTiles(array $map, array $allowedBiomes): int
    {
        $count = 0;
        foreach ($map as $tile) {
            if (
                in_array($tile->biome, $allowedBiomes, true) &&
                $tile->isPassable() &&
                $tile->resource === null
            ) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Вычисляет целевое число ресурсов без вариаций.
     */
    private function calculateResourceCount(string $resource, int $validTiles): int
    {
        $scarcity = $this->scarcity[$resource] ?? 0.0;
        return (int) round($validTiles * $this->abundance * $scarcity);
    }

    /**
     * Размещает ровно $count ресурсов типа $resource.
     */
    private function distributeResource(
        array $map,
        string $resource,
        array $allowedBiomes,
        int $count
    ): array {
        // Собираем все доступные координаты
        $positions = $this->findValidPositions($map, $allowedBiomes);
        if ($count <= 0 || empty($positions)) {
            return $map;
        }

        // Детерминированно перемешиваем список позиций
        $this->seededShuffle($positions, crc32($resource . '_' . $this->resourceSeed));

        // Берем первые $count элементов
        $selected = array_slice($positions, 0, min($count, count($positions)));

        // Устанавливаем ресурс на выбранные тайлы
        foreach ($selected as $pos) {
            $key = "{$pos->q},{$pos->r}";
            $map[$key]->setResource($resource);
        }

        return $map;
    }

    /**
     * Возвращает массив HexCoordinate для всех подходящих тайлов.
     */
    private function findValidPositions(array $map, array $allowedBiomes): array
    {
        $valid = [];
        foreach ($map as $tile) {
            if (
                in_array($tile->biome, $allowedBiomes, true) &&
                $tile->isPassable() &&
                $tile->resource === null
            ) {
                $valid[] = $tile->coordinate;
            }
        }
        return $valid;
    }

    /**
     * Fisher–Yates shuffle с детерминированным seed.
     */
    private function seededShuffle(array &$array, int $seed): void
    {
        mt_srand($seed);
        $n = count($array);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$array[$i], $array[$j]] = [$array[$j], $array[$i]];
        }
    }
}
