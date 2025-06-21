<?php

namespace App\Enums;

enum MapGenerationAlgorithm: string
{
    case PERLIN = 'perlin';
    case VORONOI = 'voronoi';
    case HYBRID = 'hybrid';
}
