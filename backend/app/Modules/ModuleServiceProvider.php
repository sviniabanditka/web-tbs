<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Module namespaces are already registered in composer.json
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load module routes
        $this->loadModuleRoutes();

        // Load module migrations
        $this->loadModuleMigrations();

        // Load module views
        $this->loadModuleViews();
    }

    /**
     * Load routes from all modules
     */
    protected function loadModuleRoutes(): void
    {
        foreach (ModuleManager::getModules() as $moduleClass) {
            $module = new $moduleClass();
            $modulePath = $module->getPath();
            $routesPath = $modulePath . '/Routes';

            if (is_dir($routesPath)) {
                $routeFiles = glob($routesPath . '/*.php');

                foreach ($routeFiles as $routeFile) {
                    Route::prefix('api')->group(function () use ($routeFile) {
                        require $routeFile;
                    });
                }
            }
        }
    }

    /**
     * Load migrations from all modules
     */
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

    /**
     * Load views from all modules
     */
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
