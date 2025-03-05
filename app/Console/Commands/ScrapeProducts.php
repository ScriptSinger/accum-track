<?php

namespace App\Console\Commands;

use App\Factories\ShopParserFactory;
use App\Models\ProductLink;
use App\Models\Shop;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;


use Illuminate\Console\Command;

class ScrapeProducts extends Command
{
    protected $signature = 'scrape:products {shop} {--categoryLinkId=*}';
    protected $description = 'Scrapes products from the specified shop';

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
        $shopName = $this->argument('shop');
        $categoryLinkIds = $this->option('categoryLinkId'); // массив ID категорий
        $shop = Shop::where('name', $shopName)->firstOrFail();
        $productLinks = $this->getProductLinks($shop, $categoryLinkIds); // массив Объектов
        $parser = $this->shopParserFactory->make($this->httpClient, $shop);

        $data = $parser->scrapeProducts($this->httpClient, $productLinks, $shop);

        $this->ShopDataRecorder->importProducts($data);
    }



    protected function getProductLinks(Shop $shop, array $categoryLinkIds = [])
    {
        if ($categoryLinkIds) {
            return ProductLink::where('shop_id', $shop->id)
                ->whereIn('category_link_id', $categoryLinkIds)
                ->get();
        }

        // Если категория не передана, берем все ссылки для магазина
        return ProductLink::where('shop_id', $shop->id)->get();
    }
}
