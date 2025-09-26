<?php

namespace App\Modules\GameEngine;

use App\Modules\Module;

class GameEngineModule extends Module
{
    public static function getName(): string
    {
        return 'GameEngine';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\GameEngine';
    }

    public static function getPath(): string
    {
        return app_path('Modules/GameEngine');
    }
}
