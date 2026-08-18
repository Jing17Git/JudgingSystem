<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CacheController extends Controller
{
    /**
     * Display cache management dashboard and system cache statistics.
     */
    public function index()
    {
        // Cache & Session Drivers
        $cacheDriver = config('cache.default');
        $sessionDriver = config('session.driver');
        $dbConnection = config('database.default');

        // Status flags
        $routesCached = app()->routesAreCached();
        $configCached = app()->configurationIsCached();
        $eventsCached = app()->eventsAreCached();

        // Database cache records
        $dbCacheCount = 0;
        if (Schema::hasTable('cache')) {
            try {
                $dbCacheCount = DB::table('cache')->count();
            } catch (\Throwable $e) {
                $dbCacheCount = 0;
            }
        }

        // Database session records
        $dbSessionsCount = 0;
        if (Schema::hasTable('sessions')) {
            try {
                $dbSessionsCount = DB::table('sessions')->count();
            } catch (\Throwable $e) {
                $dbSessionsCount = 0;
            }
        }

        // Storage Directory Stats
        $cacheDirStats = $this->getDirStats(storage_path('framework/cache/data'));
        $viewsDirStats = $this->getDirStats(storage_path('framework/views'));
        $sessionsDirStats = $this->getDirStats(storage_path('framework/sessions'));
        $logsDirStats = $this->getDirStats(storage_path('logs'));

        // Server and PHP environment info
        $opcacheInfo = null;
        if (function_exists('opcache_get_status')) {
            $status = @opcache_get_status(false);
            if ($status && isset($status['opcache_enabled']) && $status['opcache_enabled']) {
                $opcacheInfo = [
                    'enabled' => true,
                    'memory_used' => isset($status['memory_usage']['used_memory']) ? $this->formatBytes($status['memory_usage']['used_memory']) : 'N/A',
                    'memory_free' => isset($status['memory_usage']['free_memory']) ? $this->formatBytes($status['memory_usage']['free_memory']) : 'N/A',
                    'hit_rate' => isset($status['opcache_statistics']['opcache_hit_rate']) ? round($status['opcache_statistics']['opcache_hit_rate'], 1) . '%' : 'N/A',
                    'cached_scripts' => $status['opcache_statistics']['num_cached_scripts'] ?? 0,
                ];
            }
        }

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? php_uname('s'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ];

        $isOptimized = $routesCached && $configCached;

        return view('admin.cache.index', compact(
            'cacheDriver',
            'sessionDriver',
            'dbConnection',
            'routesCached',
            'configCached',
            'eventsCached',
            'isOptimized',
            'dbCacheCount',
            'dbSessionsCount',
            'cacheDirStats',
            'viewsDirStats',
            'sessionsDirStats',
            'logsDirStats',
            'opcacheInfo',
            'systemInfo'
        ));
    }

    /**
     * Clear all application and framework caches (optimize:clear).
     */
    public function clearAll()
    {
        try {
            Artisan::call('optimize:clear');
            try {
                Cache::flush();
            } catch (\Throwable $e) {
                // Ignore if flush fails
            }

            return redirect()->back()
                ->with('success', 'All system caches (Application Cache, Configuration, Routes, Views, and Compiled classes) have been cleared successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear all caches: ' . $e->getMessage());
        }
    }

    /**
     * Clear application data cache (cache:clear & Cache::flush).
     */
    public function clearApp()
    {
        try {
            Artisan::call('cache:clear');
            try {
                Cache::flush();
            } catch (\Throwable $e) {
                // Ignore
            }

            return redirect()->back()
                ->with('success', 'Application data cache cleared successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear application cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear route cache (route:clear).
     */
    public function clearRoute()
    {
        try {
            Artisan::call('route:clear');

            return redirect()->back()
                ->with('success', 'Route cache cleared successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear route cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear configuration cache (config:clear).
     */
    public function clearConfig()
    {
        try {
            Artisan::call('config:clear');

            return redirect()->back()
                ->with('success', 'Configuration cache cleared successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear configuration cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear compiled Blade views (view:clear).
     */
    public function clearView()
    {
        try {
            Artisan::call('view:clear');

            return redirect()->back()
                ->with('success', 'Compiled Blade view cache cleared successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear compiled views: ' . $e->getMessage());
        }
    }

    /**
     * Optimize application (cache routes, config, events, and views).
     */
    public function optimize()
    {
        try {
            Artisan::call('optimize');

            return redirect()->back()
                ->with('success', 'Application successfully cached and optimized (Configuration, Routes, Events, and Blade views).');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to optimize application: ' . $e->getMessage());
        }
    }

    /**
     * Clear storage log files.
     */
    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs');
            if (File::isDirectory($logPath)) {
                $files = File::files($logPath);
                foreach ($files as $file) {
                    if ($file->getFilename() === 'laravel.log') {
                        File::put($file->getRealPath(), '');
                    } else {
                        File::delete($file->getRealPath());
                    }
                }
            }

            return redirect()->back()
                ->with('success', 'Application log files cleared successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear log files: ' . $e->getMessage());
        }
    }

    /**
     * Helper to compute directory size and file count.
     */
    private function getDirStats(string $dir): array
    {
        $size = 0;
        $count = 0;

        if (is_dir($dir)) {
            try {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                        $count++;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore permissions/read issues
            }
        }

        return [
            'size' => $this->formatBytes($size),
            'raw_bytes' => $size,
            'count' => $count,
        ];
    }

    /**
     * Format bytes into readable string.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
