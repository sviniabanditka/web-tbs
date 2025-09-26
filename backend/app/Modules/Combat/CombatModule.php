<?php

namespace App\Modules\Combat;

use App\Modules\Module;

class CombatModule extends Module
{
    public static function getName(): string
    {
        return 'Combat';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\Combat';
    }

    public static function getPath(): string
    {
        return app_path('Modules/Combat');
    }
}
