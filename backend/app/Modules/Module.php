<?php

namespace App\Modules;

abstract class Module
{
    abstract public static function getName(): string;

    abstract public static function getNamespace(): string;

    abstract public static function getPath(): string;

    public static function getServiceProviders(): array
    {
        return [];
    }

    public static function getMigrationsPath(): string
    {
        return static::getPath() . '/Migrations';
    }

    public static function getViewsPath(): string
    {
        return static::getPath() . '/Views';
    }

    public static function getRoutesPath(): string
    {
        return static::getPath() . '/Routes';
    }

    public static function getRoutesPrefix(): string
    {
        return 'api';
    }

    public static function getConfigPath(): string
    {
        return static::getPath() . '/Config';
    }
}
