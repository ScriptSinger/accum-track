<?php

namespace App\Console\Commands;

use App\Factories\ShopParserFactory;
use App\Models\Shop;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;

use Illuminate\Console\Command;

class ScrapeProductLinks extends Command
{
    protected $signature = 'scrape:product-links {shop}';
    protected $description = 'Scrapes product links from the specified shop';

    protected HttpClientService $httpClient;
    protected ShopParserFactory $shopParserFactory;
    protected ShopDataRecorder $ShopDataRecorder;


    public function __construct(ShopDataRecorder $ShopDataRecorder, ShopParserFactory $shopParserFactory, HttpClientService $httpClient)
    {
        parent::__construct();
        $this->ShopDataRecorder = $ShopDataRecorder;
        $this->shopParserFactory = $shopParserFactory;
        $this->httpClient = $httpClient;
    }

    public function handle()
    {
        $this->measurePerformance(function () {

            $shopName = $this->argument('shop');
            $shop = Shop::where('name', $shopName)->firstOrFail();
            $parser = $this->shopParserFactory->make($this->httpClient, $shop);
            $data = $parser->scrapeProductLinks($this->httpClient, $shop);

            $this->ShopDataRecorder->importProductLinks($data, $shop);
        });
    }


    /**
     * Оборачивает выполнение кода замыканием и замеряет время выполнения и потребление памяти.
     *
     * @param callable $callback Функция для измерения
     */
    protected function measurePerformance(callable $callback)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $callback();

        $executionTime = round(microtime(true) - $startTime, 4);
        $memoryUsage = round((memory_get_usage() - $startMemory) / 1024 / 1024, 2);

        $this->info("Обработка завершена за {$executionTime} секунд");
        $this->info("Потребление памяти: {$memoryUsage} МБ");
    }
}
