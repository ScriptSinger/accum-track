<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Factories\ShopParserFactory;
use App\Services\HttpClientService;
use App\Services\Importers\ShopDataRecorder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeCategoryLinksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $shopId;

    /**
     * Создаём задачу, сохраняем только сериализуемые данные.
     *
     * @param int $shopId Идентификатор магазина
     */
    public function __construct(int $shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * Выполняется в момент обработки задачи.
     * Здесь зависимости разрешаются через контейнер (инъекция в метод handle).
     *
     * @param HttpClientService   $httpClient
     * @param ShopParserFactory   $shopParserFactory
     * @param ShopDataRecorder    $shopDataRecorder
     */
    public function handle(
        HttpClientService $httpClient,
        ShopParserFactory $shopParserFactory,
        ShopDataRecorder $shopDataRecorder
    ) {
        // Получаем магазин по идентификатору
        $shop = Shop::findOrFail($this->shopId);

        // Создаем парсер, используя необходимые сервисы
        $parser = $shopParserFactory->make($httpClient, $shop);

        // Выполняем сбор данных о ссылках категорий
        $data = $parser->scrapeCategoryLinks($httpClient, $shop);

        // Импортируем полученные данные в БД
        $shopDataRecorder->importCategoryLinks($data, $shop);
    }
}
