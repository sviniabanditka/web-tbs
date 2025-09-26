<?php

namespace App\Modules\Campaign;

use App\Modules\Module;

class CampaignModule extends Module
{
    public static function getName(): string
    {
        return 'Campaign';
    }

    public static function getNamespace(): string
    {
        return 'App\\Modules\\Campaign';
    }

    public static function getPath(): string
    {
        return app_path('Modules/Campaign');
    }

    public static function getRoutesPrefix(): string
    {
        return 'api/campaigns';
    }
}
