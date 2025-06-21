<?php

namespace App\Enums;

enum BuildingType: string
{
    case CITY = 'city';
    case FARM = 'farm';
    case MINE = 'mine';
    case BARRACKS = 'barracks';
    case TEMPLE = 'temple';
}
