<?php

use App\Enums\UnitType;
use App\Enums\BuildingType;
use App\Enums\ResourceType;

return [
    'starting_resources' => [
        ResourceType::GOLD->value => 100,
        ResourceType::FOOD->value => 100,
        ResourceType::WOOD->value => 50,
        ResourceType::STONE->value => 50,
        ResourceType::IRON->value => 0,
    ],

    'units' => [
        UnitType::WARRIOR->value => [
            'name' => 'Warrior',
            'stats' => [
                'health' => 100,
                'attack' => 10,
                'defense' => 8,
                'movement_range' => 2,
            ],
            'costs' => [
                ResourceType::GOLD->value => 50,
                ResourceType::FOOD->value => 20,
            ],
        ],
        UnitType::ARCHER->value => [
            'name' => 'Archer',
            'stats' => [
                'health' => 80,
                'attack' => 12,
                'defense' => 5,
                'movement_range' => 2,
            ],
            'costs' => [
                ResourceType::GOLD->value => 60,
                ResourceType::WOOD->value => 20,
            ],
        ],
        UnitType::SETTLER->value => [
            'name' => 'Settler',
            'stats' => [
                'health' => 50,
                'attack' => 0,
                'defense' => 3,
                'movement_range' => 2,
            ],
            'costs' => [
                ResourceType::GOLD->value => 100,
                ResourceType::FOOD->value => 80,
            ],
        ],
    ],

    'buildings' => [
        BuildingType::BARRACKS->value => [
            'name' => 'Barracks',
            'costs' => [
                ResourceType::GOLD->value => 100,
                ResourceType::WOOD->value => 50,
                ResourceType::STONE->value => 50,
            ],
            'produces' => [],
        ],
        BuildingType::FARM->value => [
            'name' => 'Farm',
            'costs' => [
                ResourceType::GOLD->value => 50,
                ResourceType::WOOD->value => 30,
            ],
            'produces' => [
                ResourceType::FOOD->value => 10,
            ],
        ],
        BuildingType::MINE->value => [
            'name' => 'Mine',
            'costs' => [
                ResourceType::GOLD->value => 80,
                ResourceType::WOOD->value => 40,
            ],
            'produces' => [
                ResourceType::GOLD->value => 5, // Or could be iron if the mine is on an iron deposit
            ],
        ],
    ],
];
