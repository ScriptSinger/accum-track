<?php

namespace App\Factories;


use App\Models\Shop;
use App\Interfaces\ShopParserInterface;
use App\Services\Parsers\StartStopParser;
use App\Services\Parsers\UfaAkbParser;
use App\Services\HttpClientService;

use Exception;

class ShopParserFactory
{
    /**
     * Создает экземпляр парсера для указанного магазина.
     *
     * @param Shop $shop
     * @param HttpClientService $httpClient
     * @return ShopParserInterface
     * @throws Exception
     */
    public static function make(Shop $shop, HttpClientService $httpClient): ShopParserInterface
    {
        return match ($shop->name) {
                // 'Старт стоп' => new StartStopParser($httpClient),
                // 'Мир аккумуляторов' => new UfaAkbParser($httpClient),
            default => throw new Exception("Парсер для {$shop->name} не найден"),
        };
    }
}
