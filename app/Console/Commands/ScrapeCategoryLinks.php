<?php

namespace App\Console\Commands;

use App\Factories\ShopParserFactory;
use App\Models\Shop;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;
use Illuminate\Console\Command;

class ScrapeCategoryLinks extends Command
{
    protected $signature = 'scrape:category-links {shop}';
    protected $description = 'Scrapes category links from the specified shop';

    protected HttpClientService $httpClient;
    protected ShopParserFactory $shopParserFactory;
    protected ShopDataRecorder $shopDataRecorder;

    public function __construct(
        ShopDataRecorder $shopDataRecorder,
        ShopParserFactory $shopParserFactory,
        HttpClientService $httpClient
    ) {
        parent::__construct();
        $this->shopDataRecorder  = $shopDataRecorder;
        $this->shopParserFactory = $shopParserFactory;
        $this->httpClient        = $httpClient;
    }

    public function handle()
    {
        $shop = $this->argument('shop');
        $shop = Shop::where('name', $shop)->firstOrFail();

        $parser = $this->shopParserFactory->make($this->httpClient, $shop);


        // Предполагается, что у парсера реализован метод scrapeCategoryLinks()
        $data = $parser->scrapeCategoryLinks($this->httpClient, $shop);


        // Импорт ссылок категорий в БД
        $this->shopDataRecorder->importCategoryLinks($data, $shop);
    }
}
