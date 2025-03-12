<?php

namespace App\Jobs;

use App\Factories\ShopParserFactory;
use App\Models\ProductLink;
use App\Models\Shop;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ScrapeProductsJob implements ShouldQueue
{
    use Queueable;


    protected $categoryLinkIds;
    protected $shopId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $shopId, array $categoryLinkIds,)
    {
        $this->categoryLinkIds = $categoryLinkIds;
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


        try {

            $shop = Shop::findOrFail($this->shopId);
            $productLinks = $this->getProductLinks($shop, $this->categoryLinkIds); // массив Объектов
            $parser = $shopParserFactory->make($httpClient, $shop);
            $data = $parser->scrapeProducts($httpClient, $productLinks, $shop);
            $shopDataRecorder->importProducts($data);
        } catch (Throwable $e) {
            Log::error("Ошибка в ScrapeProductsJob: " . $e->getMessage(), [
                'shopId' => $this->shopId,
                'categoryLinkIds' => $this->categoryLinkIds,
                'trace' => $e->getTraceAsString()
            ]);
            $this->fail($e);
        }
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
