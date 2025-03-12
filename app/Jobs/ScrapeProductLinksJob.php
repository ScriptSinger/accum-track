<?php

namespace App\Jobs;

use App\Factories\ShopParserFactory;
use App\Models\Shop;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScrapeProductLinksJob implements ShouldQueue
{
    use Queueable;

    protected int $shopId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $shopId)
    {
        $this->shopId = $shopId;
    }


    /**
     * Execute the job.
     */
    public function handle(
        HttpClientService $httpClient,
        ShopParserFactory $shopParserFactory,
        ShopDataRecorder $shopDataRecorder
    ): void {

        $shop = Shop::findOrFail($this->shopId);
        $parser = $shopParserFactory->make($httpClient, $shop);
        $data = $parser->scrapeProductLinks($httpClient, $shop);
        $shopDataRecorder->importProductLinks($data, $shop);
    }
}
