<?php

namespace App\Modules;

use App\Modules\Auth\AuthModule;
use App\Modules\Campaign\CampaignModule;
use App\Modules\GameEngine\GameEngineModule;
use App\Modules\MapGeneration\MapGenerationModule;
use App\Modules\Combat\CombatModule;
use App\Modules\Chat\ChatModule;
use App\Modules\Notifications\NotificationsModule;
use App\Modules\Analytics\AnalyticsModule;
use App\Modules\Common\CommonModule;

class ModuleManager
{
    public static function getModules(): array
    {
        return [
            CommonModule::class,
            AuthModule::class,
            CampaignModule::class,
            GameEngineModule::class,
            MapGenerationModule::class,
            CombatModule::class,
            ChatModule::class,
            NotificationsModule::class,
            AnalyticsModule::class,
        ];
    }

    public static function getModule(string $name): ?Module
    {
        foreach (static::getModules() as $moduleClass) {
            if ($moduleClass::getName() === $name) {
                return new $moduleClass();
            }
        }

        return null;
    }

    public static function getNamespaces(): array
    {
        $namespaces = [];

        foreach (static::getModules() as $moduleClass) {
            $module = new $moduleClass();
            $namespaces[$module->getNamespace() . '\\'] = $module->getPath() . '/';
        }

        return $namespaces;
    }
}
