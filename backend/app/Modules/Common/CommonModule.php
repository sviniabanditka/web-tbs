<?php

namespace App\Modules\Common;

use App\Modules\Module;

class CommonModule extends Module
{
    public static function getName(): string
    {
        return 'Common';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\Common';
    }

    public static function getPath(): string
    {
        return app_path('Modules/Common');
    }
}
