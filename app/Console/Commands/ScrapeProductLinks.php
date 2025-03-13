<?php

namespace App\Console\Commands;

use App\Events\ProductLinksScraped;
use App\Jobs\ScrapeProductLinksJob;
use App\Models\Shop;

use Illuminate\Console\Command;

class ScrapeProductLinks extends Command
{
    protected $signature = 'scrape:product-links {shop}';
    protected $description = 'Scrapes product links from the specified shop';





    public function handle()
    {
        $shopName = $this->argument('shop');
        $shop = Shop::where('name', $shopName)->firstOrFail();
        ScrapeProductLinksJob::dispatch($shop->id);
        $this->info("Job dispatched for shop: {$shop->name}");

        // Запускаем событие
        event(new ProductLinksScraped());
    }
}
