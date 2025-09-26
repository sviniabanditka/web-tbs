<?php

namespace App\Modules\Common\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Modules\ModuleManager;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'checks' => []
        ];

        // Check database connection
        $health['checks']['database'] = $this->checkDatabase();

        // Check cache
        $health['checks']['cache'] = $this->checkCache();

        // Check Redis (if configured)
        $health['checks']['redis'] = $this->checkRedis();

        // Check modules
        $health['checks']['modules'] = $this->checkModules();

        // Check storage
        $health['checks']['storage'] = $this->checkStorage();

        // Check queue (if configured)
        $health['checks']['queue'] = $this->checkQueue();

        // Determine overall status
        $hasErrors = collect($health['checks'])->contains(function ($check) {
            return $check['status'] === 'unhealthy';
        });

        if ($hasErrors) {
            $health['status'] = 'unhealthy';
        }

        $statusCode = $hasErrors ? 503 : 200;

        return response()->json($health, $statusCode);
    }

    private function checkDatabase(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            $driver = $connection->getDriverName();

            // Get table count based on database type
            $tablesCount = $this->getTablesCount($connection, $driver);

            return [
                'status' => 'healthy',
                'message' => 'Database connection successful',
                'driver' => $driver,
                'tables_count' => $tablesCount
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    private function getTablesCount($connection, string $driver): int
    {
        try {
            switch ($driver) {
                case 'mysql':
                    $tables = $connection->select('SHOW TABLES');
                    return count($tables);

                case 'pgsql':
                    $tables = $connection->select("
                        SELECT table_name
                        FROM information_schema.tables
                        WHERE table_schema = 'public'
                    ");
                    return count($tables);

                case 'sqlite':
                    $tables = $connection->select("
                        SELECT name
                        FROM sqlite_master
                        WHERE type = 'table' AND name NOT LIKE 'sqlite_%'
                    ");
                    return count($tables);

                case 'sqlsrv':
                    $tables = $connection->select("
                        SELECT TABLE_NAME
                        FROM INFORMATION_SCHEMA.TABLES
                        WHERE TABLE_TYPE = 'BASE TABLE'
                    ");
                    return count($tables);

                default:
                    // For unknown drivers, just return 0
                    return 0;
            }
        } catch (\Exception $e) {
            // If we can't get table count, return 0 but don't fail the health check
            return 0;
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . time();
            $value = 'test_value';

            Cache::put($key, $value, 60);
            $retrieved = Cache::get($key);
            Cache::forget($key);

            if ($retrieved === $value) {
                return [
                    'status' => 'healthy',
                    'message' => 'Cache is working properly',
                    'driver' => config('cache.default')
                ];
            } else {
                return [
                    'status' => 'unhealthy',
                    'message' => 'Cache read/write test failed'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Cache check failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkRedis(): array
    {
        try {
            $cacheDriver = config('cache.default');
            $queueDriver = config('queue.default');
            $sessionDriver = config('session.driver');

            $usesRedis = in_array('redis', [$cacheDriver, $queueDriver, $sessionDriver]);

            if ($usesRedis) {
                try {
                    // Test Redis connection through Cache facade
                    $testKey = 'health_check_redis_' . time();
                    $testValue = 'test_value';

                    Cache::store('redis')->put($testKey, $testValue, 60);
                    $retrieved = Cache::store('redis')->get($testKey);
                    Cache::store('redis')->forget($testKey);

                    if ($retrieved === $testValue) {
                        return [
                            'status' => 'healthy',
                            'message' => 'Redis connection successful',
                            'client' => config('database.redis.client', 'unknown'),
                            'used_for' => array_filter([
                                'cache' => $cacheDriver === 'redis' ? 'yes' : null,
                                'queue' => $queueDriver === 'redis' ? 'yes' : null,
                                'session' => $sessionDriver === 'redis' ? 'yes' : null,
                            ])
                        ];
                    } else {
                        return [
                            'status' => 'unhealthy',
                            'message' => 'Redis read/write test failed'
                        ];
                    }
                } catch (\Exception $e) {
                    return [
                        'status' => 'unhealthy',
                        'message' => 'Redis connection failed: ' . $e->getMessage(),
                        'client' => config('database.redis.client', 'unknown')
                    ];
                }
            } else {
                return [
                    'status' => 'healthy',
                    'message' => 'Redis not configured (not required)',
                    'cache_driver' => $cacheDriver,
                    'queue_driver' => $queueDriver,
                    'session_driver' => $sessionDriver
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Redis connection failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkModules(): array
    {
        try {
            $modules = ModuleManager::getModules();
            $moduleStatuses = [];
            $allHealthy = true;

            foreach ($modules as $moduleClass) {
                $module = new $moduleClass();
                $moduleName = $module->getName();

                // Check if module files exist
                $modulePath = $module->getPath();
                $exists = is_dir($modulePath);

                $moduleStatuses[$moduleName] = [
                    'exists' => $exists,
                    'path' => $modulePath,
                    'namespace' => $module->getNamespace()
                ];

                if (!$exists) {
                    $allHealthy = false;
                }
            }

            return [
                'status' => $allHealthy ? 'healthy' : 'unhealthy',
                'message' => $allHealthy ? 'All modules loaded successfully' : 'Some modules have issues',
                'modules' => $moduleStatuses,
                'total_modules' => count($modules)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Module check failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkStorage(): array
    {
        try {
            $storagePath = storage_path();
            $writable = is_writable($storagePath);

            $logsPath = storage_path('logs');
            $logsWritable = is_writable($logsPath);

            $appPath = storage_path('app');
            $appWritable = is_writable($appPath);

            $allWritable = $writable && $logsWritable && $appWritable;

            return [
                'status' => $allWritable ? 'healthy' : 'unhealthy',
                'message' => $allWritable ? 'Storage is writable' : 'Storage permissions issue',
                'details' => [
                    'storage_writable' => $writable,
                    'logs_writable' => $logsWritable,
                    'app_writable' => $appWritable
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Storage check failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkQueue(): array
    {
        try {
            $queueDriver = config('queue.default');

            if ($queueDriver === 'sync') {
                return [
                    'status' => 'healthy',
                    'message' => 'Queue is using sync driver (no external dependency)',
                    'driver' => $queueDriver
                ];
            }

            // For other drivers, we could add specific checks
            return [
                'status' => 'healthy',
                'message' => 'Queue driver configured',
                'driver' => $queueDriver
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Queue check failed: ' . $e->getMessage()
            ];
        }
    }
}
