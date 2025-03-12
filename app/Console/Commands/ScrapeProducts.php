<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeProductsJob;
use App\Models\Shop;



use Illuminate\Console\Command;

class ScrapeProducts extends Command
{
    protected $signature = 'scrape:products {shop} {--categoryLinkId=*}';
    protected $description = 'Scrapes products from the specified shop';


    public function handle()
    {

        $shopName = $this->argument('shop');
        $categoryLinkIds = $this->option('categoryLinkId'); // массив ID категорий
        $shop = Shop::where('name', $shopName)->firstOrFail();


        ScrapeProductsJob::dispatch($shop->id, $categoryLinkIds);
        $this->info("Job dispatched for shop: {$shop->name}");
    }
}
