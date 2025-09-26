<?php

namespace App\Modules\Analytics;

use App\Modules\Module;

class AnalyticsModule extends Module
{
    public static function getName(): string
    {
        return 'Analytics';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\Analytics';
    }

    public static function getPath(): string
    {
        return app_path('Modules/Analytics');
    }
}
