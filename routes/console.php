<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->everyThirtySeconds();


Schedule::command('scrape:category-links start-stop')->everyMinute();
Schedule::command('scrape:product-links start-stop');
Schedule::command('scrape:products start-stop --categoryLinkId=1');
