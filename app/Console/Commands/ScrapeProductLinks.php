<?php

namespace App\Console\Commands;

use App\Factories\ShopParserFactory;
use App\Models\Shop;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;
use App\Services\Utils\PerformanceService;
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

    public function handle(PerformanceService $performanceService)
    {
        $shopName = $this->argument('shop');

        // Обертываем всю операцию в замер производительности
        $performanceService->measure(function () use ($shopName) {


            $shop = Shop::where('name', $shopName)->firstOrFail();
            $parser = $this->shopParserFactory->make($this->httpClient, $shop);
            $data = $parser->scrapeProductLinks($this->httpClient, $shop);
            $this->ShopDataRecorder->importProductLinks($data, $shop);


            //
        }, $this->output, "Обработка магазина: {$shopName}");
    }
}
