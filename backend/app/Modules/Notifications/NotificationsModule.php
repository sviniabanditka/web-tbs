<?php

namespace App\Modules\Notifications;

use App\Modules\Module;

class NotificationsModule extends Module
{
    public static function getName(): string
    {
        return 'Notifications';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\Notifications';
    }

    public static function getPath(): string
    {
        return app_path('Modules/Notifications');
    }
}
