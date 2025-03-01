<?php

namespace App\Factories;

use App\Interfaces\ShopParserInterface;
use App\Models\Shop;
use App\Services\HttpClientService;

use Exception;

class ShopParserFactory
{
    public static function make(HttpClientService $httpClient, Shop $shop)
    {
        $parsers = config('parsers');

        if (!isset($parsers[$shop->name])) {
            throw new Exception("Парсер для {$shop->name} не найден.");
        }

        return new $parsers[$shop->name]($httpClient);
    }
}
