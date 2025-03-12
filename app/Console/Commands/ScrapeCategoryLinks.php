<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeCategoryLinksJob;
use App\Models\Shop;
use Illuminate\Console\Command;

class ScrapeCategoryLinks extends Command
{
    protected $signature = 'scrape:category-links {shop}';
    protected $description = 'Scrapes category links from the specified shop';



    public function handle()
    {

        $shopName = $this->argument('shop');
        $shop = Shop::where('name', $shopName)->firstOrFail();
        ScrapeCategoryLinksJob::dispatch($shop->id);
        $this->info("Job dispatched for shop: {$shop->name}");
    }
}
