<?php

namespace App\Listeners;

use App\Events\ProductLinksScraped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class RunProductsScraper
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductLinksScraped $event): void
    {
        // Запускаем следующую команду
        Artisan::call('scrape:products start-stop --categoryLinkId=1');
    }
}
