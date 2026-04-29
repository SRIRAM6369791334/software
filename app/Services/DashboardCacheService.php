<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DashboardCacheService
{
    /**
     * Cache key for dashboard stats.
     */
    private const CACHE_KEY = 'dashboard_stats';

    /**
     * Cache duration in seconds (5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * Get cached stats or execute callback to refresh.
     *
     * @param callable $callback
     * @return mixed
     */
    public function getStats(callable $callback)
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, $callback);
    }

    /**
     * Clear the dashboard cache.
     * Useful when new data is added.
     */
    public function clear(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
