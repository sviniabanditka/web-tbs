<?php

namespace App\Modules;

abstract class Module
{
    /**
     * Get the module name
     */
    abstract public static function getName(): string;

    /**
     * Get the module namespace
     */
    abstract public static function getNamespace(): string;

    /**
     * Get the module path
     */
    abstract public static function getPath(): string;


    /**
     * Get module service providers
     */
    public static function getServiceProviders(): array
    {
        return [];
    }

    /**
     * Get module migrations path
     */
    public static function getMigrationsPath(): string
    {
        return static::getPath() . '/Migrations';
    }

    /**
     * Get module views path
     */
    public static function getViewsPath(): string
    {
        return static::getPath() . '/Views';
    }

    /**
     * Get module config path
     */
    public static function getConfigPath(): string
    {
        return static::getPath() . '/Config';
    }
}
