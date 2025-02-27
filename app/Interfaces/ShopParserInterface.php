<?php

namespace App\Interfaces;

use App\Services\HttpClientService;

interface ShopParserInterface
{
    /**
     * Конструктор парсера с внедрением HTTP-клиента.
     */
    public function __construct(HttpClientService $httpClient);

    /**
     * Метод для парсинга страницы магазина.
     * 
     * @param string $url URL страницы для парсинга.
     * @return array Данные, извлеченные с сайта.
     */
    public function scrape(string $url): array;
}
