<?php

namespace App\Listeners;

use App\Events\CategoryLinksScraped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class RunProductLinksScraper
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
    public function handle(CategoryLinksScraped $event): void
    {
        // Запускаем следующую команду
        Artisan::call('scrape:product-links start-stop');
    }
}
