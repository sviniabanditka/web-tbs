<?php

namespace App\Modules\Chat;

use App\Modules\Module;

class ChatModule extends Module
{
    public static function getName(): string
    {
        return 'Chat';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\Chat';
    }

    public static function getPath(): string
    {
        return app_path('Modules/Chat');
    }
}
