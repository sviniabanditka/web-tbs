<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadModuleRoutes();
        $this->loadModuleMigrations();
        $this->loadModuleViews();
    }

    protected function loadModuleRoutes(): void
    {
        foreach (ModuleManager::getModules() as $moduleClass) {
            $module = new $moduleClass();
            $routesPath = $module->getRoutesPath();
            $prefix = $module->getRoutesPrefix();

            if (is_dir($routesPath)) {
                $routeFiles = glob($routesPath . '/*.php');

                foreach ($routeFiles as $routeFile) {
                    Route::prefix($prefix)->group(function () use ($routeFile) {
                        require $routeFile;
                    });
                }
            }
        }
    }

    protected function loadModuleMigrations(): void
    {
        foreach (ModuleManager::getModules() as $moduleClass) {
            $module = new $moduleClass();
            $migrationsPath = $module->getMigrationsPath();

            if (is_dir($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }
    }

    protected function loadModuleViews(): void
    {
        foreach (ModuleManager::getModules() as $moduleClass) {
            $module = new $moduleClass();
            $viewsPath = $module->getViewsPath();

            if (is_dir($viewsPath)) {
                $moduleName = strtolower($module->getName());
                $this->loadViewsFrom($viewsPath, $moduleName);
            }
        }
    }
}
