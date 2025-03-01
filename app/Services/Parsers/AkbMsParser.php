<?php

namespace App\Services\Parsers;

use App\Models\Shop;
use App\Services\HttpClientService;
use Symfony\Component\DomCrawler\Crawler;

class AkbMsParser
{
    public function scrapeProductLinks(HttpClientService $httpClient, Shop $shop): array
    {
        $response = $httpClient->get($shop->url); // Получаем HTML-контент страницы
        $html = $response->getBody()->getContents();

        // Инициализируем Crawler
        $crawler = new Crawler($html);

        // Извлекаем все ссылки на страницы товаров с помощью регулярного выражения
        $links = $crawler->filter('a')->each(function (Crawler $node) {
            $href = $node->attr('href');
            // Используем регулярное выражение: ищем ссылки вида "/products/<цифры>"
            if (preg_match('#/products/\d+#', $href)) {
                return $href;
            }
            return null;
        });

        // Убираем null-значения из массива и удаляем дубликаты
        return array_values(array_unique(array_filter($links)));
    }
}
