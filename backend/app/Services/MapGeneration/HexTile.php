<?php

namespace App\Services\MapGeneration;
/**
 * Represents a single tile of a hexagonal map
 */
class HexTile
{
    public HexCoordinate $coordinate;
    public string $biome;
    public ?string $resource;
    public bool $isPassable;
    public bool $isBridge = false;

    public function __construct(HexCoordinate $coordinate)
    {
        $this->coordinate = $coordinate;
        $this->biome = 'grass';
        $this->resource = null;
        $this->isPassable = true;
    }

    public function setBiome(string $biome): void
    {
        $this->biome = $biome;
        $this->isPassable = ($biome !== 'water') || $this->isBridge;
    }

    public function setResource(?string $resource): void
    {
        $this->resource = $resource;
    }

    public function setBridge(bool $isBridge): void
    {
        $this->isBridge = $isBridge;
        if ($isBridge && $this->biome === 'water') {
            $this->isPassable = true;
        }
    }

    public function isPassable(): bool
    {
        return $this->isPassable;
    }
}
