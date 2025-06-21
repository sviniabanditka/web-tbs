<?php

namespace App\Enums;

enum TerrainType: string
{
    case GRASS = 'grass';
    case FOREST = 'forest';
    case MOUNTAIN = 'mountain';
    case WATER = 'water';
    case DESERT = 'desert';
    case TUNDRA = 'tundra';
}
