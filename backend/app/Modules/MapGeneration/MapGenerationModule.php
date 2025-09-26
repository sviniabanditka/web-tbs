<?php

namespace App\Modules\MapGeneration;

use App\Modules\Module;

class MapGenerationModule extends Module
{
    public static function getName(): string
    {
        return 'MapGeneration';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\MapGeneration';
    }

    public static function getPath(): string
    {
        return app_path('Modules/MapGeneration');
    }
}
