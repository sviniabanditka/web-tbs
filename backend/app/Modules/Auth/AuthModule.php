<?php

namespace App\Modules\Auth;

use App\Modules\Module;

class AuthModule extends Module
{
    public static function getName(): string
    {
        return 'Auth';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\Auth';
    }

    public static function getPath(): string
    {
        return app_path('Modules/Auth');
    }

    public static function getViewsPath(): string
    {
        return static::getPath() . '/Views';
    }
}
