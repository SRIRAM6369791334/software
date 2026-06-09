<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * Binds Repository Interfaces → Eloquent Implementations (Clean Architecture).
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\StockRepositoryInterface::class,
            \App\Repositories\StockRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PurchaseRepositoryInterface::class,
            \App\Repositories\PurchaseRepository::class
        );
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // \Illuminate\Pagination\Paginator::useBootstrapFive();
        \Illuminate\Support\Facades\RateLimiter::for('billing', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('payments', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\Item::observe(\App\Observers\ItemObserver::class);
    }
}
