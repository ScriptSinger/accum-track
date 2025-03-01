<?php

namespace App\Services\Parsers;

use App\Models\Shop;
use App\Services\HttpClientService;
use Symfony\Component\DomCrawler\Crawler;

class StartStopParser
{
    /**
     * Извлекает ссылки первого уровня категорий.
     *
     * @param HttpClientService $httpClient
     * @param Shop $shop
     * @return array Массив категорий, где каждая категория представлена как ['name' => ..., 'url' => ...]
     */
    public function scrapeCategoryLinks(HttpClientService $httpClient, Shop $shop): array
    {
        // Получаем HTML-контент главной страницы
        $response = $httpClient->get($shop->url);
        $html = $response->getBody()->getContents();

        // Инициализируем Crawler
        $crawler = new Crawler($html);

        // Выбираем ссылки первого уровня из главного меню
        $categoryLinks = $crawler
            ->filter('ul.menu__collapse.main-menu__collapse li > a.menu__level-1-a')
            ->each(function (Crawler $node) use ($shop) {
                $name = trim($node->text());
                $href = $node->attr('href');

                if (empty($href)) {
                    return null;
                }

                // Если ссылка относительная, преобразуем её в абсолютную
                if (strpos($href, 'http') !== 0) {
                    $href = rtrim($shop->url, '/') . '/' . ltrim($href, '/');
                }

                return [
                    'name' => $name,
                    'url'  => $href,
                ];
            });

        // Фильтруем null и удаляем дубликаты
        $categoryLinks = array_values(array_unique(array_filter($categoryLinks), SORT_REGULAR));

        return $categoryLinks;
    }


    public function scrapeProductLinks(HttpClientService $httpClient, Shop $shop) {}
}
