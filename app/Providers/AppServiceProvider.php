<?php

namespace App\Providers;

use App\Factories\ShopParserFactory;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;
use App\Services\Utils\PerformanceService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}



    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
