<?php

namespace App\Enums;

enum ResourceType: string
{
    case IRON = 'iron';
    case GOLD = 'gold';
    case FOOD = 'food';
    case WOOD = 'wood';
    case STONE = 'stone';
}
