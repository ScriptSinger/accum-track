<?php

namespace App\Providers;

use App\Services\HttpClientService;
use App\Services\Utils\PerformanceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PerformanceService::class);
        $this->app->singleton(HttpClientService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
