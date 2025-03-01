<?php

namespace App\Interfaces;

use App\Services\HttpClientService;

interface ShopParserInterface
{
    /**
     * Метод для парсинга страницы магазина.
     * 
     * @param string $url URL страницы для парсинга.
     * @return array Данные, извлеченные с сайта.
     */
    public function scrapeProductLinks(HttpClientService $httpClient, string $url): array;
}
