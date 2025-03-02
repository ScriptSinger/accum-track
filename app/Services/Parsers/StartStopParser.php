<?php

namespace App\Services\Parsers;

use App\Models\CategoryLink;
use App\Models\Shop;
use App\Services\HttpClientService;
use Illuminate\Support\Facades\Log;
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



    public function scrapeProductLinks(HttpClientService $httpClient, Shop $shop): array
    {
        $productLinks = [];

        // Получаем все ссылки на категории из базы данных
        $categories = CategoryLink::where('shop_id', $shop->id)->get();

        foreach ($categories as $category) {
            $categoryUrl = $category->category_url;
            $categoryId = $category->id;

            // Получаем HTML первой страницы категории
            $response = $httpClient->get($categoryUrl);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Определяем максимальный номер страницы
            $maxPageNumbers = $crawler->filter('ul.pagination li a')->each(function (Crawler $node) {
                $href = $node->attr('href');
                preg_match('/page=(\d+)/', $href, $matches);
                return isset($matches[1]) ? (int)$matches[1] : null;
            });

            $maxPageNumbers = array_filter($maxPageNumbers); // Убираем null
            $maxPage = !empty($maxPageNumbers) ? max($maxPageNumbers) : 1; // Если пусто, устанавливаем 1

            // Перебираем все страницы от 1 до maxPage
            for ($page = 1; $page <= $maxPage; $page++) {
                $pageUrl = $page === 1 ? $categoryUrl : $categoryUrl . '?page=' . $page;

                // Получаем HTML текущей страницы
                $response = $httpClient->get($pageUrl);
                $html = $response->getBody()->getContents();
                $crawler = new Crawler($html);

                // Извлекаем ссылки на продукты
                $links = $crawler->filter('div.product-thumb.uni-item div.product-thumb__image a')->each(function (Crawler $node) {
                    return $node->attr('href');
                });

                // Если ссылки не найдены, вставляем null
                if (empty($links)) {
                    $links = [null];
                    Log::warning("Категория без товаров: {$category->name} ({$categoryUrl})"); // Записываем в логи
                }

                // Добавляем ссылки в общий массив, избегая дубликатов
                foreach ($links as $link) {
                    if (!in_array($link, $productLinks)) {
                        $productLinks[] = [
                            'category_link_id' => $categoryId,
                            'url' => $link
                        ];
                    }
                }
            }
        }

        return $productLinks;
    }



    public function scrapeProducts(HttpClientService $httpClient, $productLinks)
    {
        $productDetails = [];

        foreach ($productLinks as $productLinkData) {
            $productLink = $productLinkData['url'];

            if (!$productLink) {
                continue;
            }

            $response = $httpClient->get($productLink);

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $nodes = $crawler->filter('.product-data__item-div');

            dump($crawler->html());
            exit;


            // Извлекаем основные данные о товаре
            $productName = $crawler->filter('h1.product-title')->count()
                ? $crawler->filter('h1.product-title')->text()
                : 'Название не найдено';


            $productPrice = $crawler->filter('span.product-price')->count()
                ? $crawler->filter('span.product-price')->text()
                : 'Цена не найдена';



            $productDescription = $crawler->filter('div.product-description')->text();

            // Извлекаем характеристики
            $specifications = $this->parseProductSpecifications($html);

            // Добавляем в массив результатов
            $productDetails[] = [
                'url' => $productLink,
                'name' => $productName,
                'price' => $productPrice,
                'description' => $productDescription,
                'category_link_id' => $productLinkData['category_link_id'],
                'specifications' => $specifications,
            ];

            break; // Выход после первой итерации
        }

        dd($productDetails); // Отладочный вывод
        return $productDetails;
    }


    /**
     * Парсинг характеристик продукта.
     *
     * @param string $html HTML-код страницы товара.
     * @return array Ассоциативный массив характеристик.
     */
    public function parseProductSpecifications(string $html): array
    {
        $crawler = new Crawler($html);
        $specifications = [];

        // Проверяем, есть ли секция характеристик
        $crawler->filter('.product-data__item')->each(function (Crawler $node) use (&$specifications) {
            $key = trim($node->filter('.product-data__item-div')->eq(0)->text());
            $value = trim($node->filter('.product-data__item-div')->eq(1)->text());

            if (!empty($key) && !empty($value)) {
                $specifications[$key] = $value;
            }
        });

        return $specifications;
    }
}
