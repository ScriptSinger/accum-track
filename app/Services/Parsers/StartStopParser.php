<?php

namespace App\Services\Parsers;

use App\Models\CategoryLink;
use App\Models\Shop;
use App\Services\HttpClientService;
use Illuminate\Database\Eloquent\Collection;
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



    public function scrapeProducts(HttpClientService $httpClient, Collection $productLinks, Shop $shop)
    {
        $productDetails = [];


        foreach ($productLinks as $productLink) {
            $url = $productLink['url'];
            if (!$url) {
                continue;
            }
            $response = $httpClient->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $result = [];


            // 1. Извлечение характеристик продукта
            $crawler->filter('.product-data__item')->each(function (Crawler $node) use (&$result) {
                $items = $node->filter('.product-data__item-div');
                if ($items->count() > 1) {
                    $key = trim($items->eq(0)->text());
                    $value = trim($items->eq(1)->text());
                    $result[$key] = $value;
                }
            });

            // 2. Извлечение заголовка страницы (h1) и установка его как name продукта
            if ($crawler->filter('h1')->count() > 0) {
                $result['name'] = trim($crawler->filter('h1')->first()->text());
            } else {
                $result['name'] = '';
            }

            // 3. Добавление идентификаторов магазина и ссылки на продукт
            $result['shop_id'] = $shop->id;
            $result['product_link_id'] = $productLink->id;


            // 4. Новый блок: Извлечение цен (обычная и trade-in)
            $priceElement = $crawler->filter('.product-page__price.price');
            $tradeInElement = $crawler->filter('.option__val');

            if ($priceElement->count() > 0) {
                // Обычная цена извлекается из атрибута data-price
                $regularPrice = $priceElement->attr('data-price');

                // Trade-in цена ищется в .option__val, извлекаем числовое значение
                $tradeInPrice = null;
                if ($tradeInElement->count() > 0) {
                    $tradeInText = trim($tradeInElement->text());
                    preg_match('/\d+/', $tradeInText, $matches);
                    if (!empty($matches)) {
                        $tradeInPrice = $matches[0]; // Первое найденное число — это цена
                    }
                }


                // Формирование структуры для цен, соответствующей схеме таблицы prices
                $result['price'] = [
                    'price'          => $regularPrice,     // Маппинг в колонку "price"
                    'trade_in_price' => $tradeInPrice,     // Маппинг в колонку "trade_in_price"
                    'currency'       => 'RUB',
                    'date'           => date('Y-m-d')
                ];
            } else {
                // Если блок с ценой отсутствует, устанавливаем значения по умолчанию
                $result['price'] = [
                    'price'          => null,
                    'trade_in_price' => null,
                    'currency'       => 'RUB',
                    'date'           => date('Y-m-d')
                ];
            }

            $productDetails[] = $result;
        }

        // Применяем маппинг для магазина 'start-stop'
        return $this->mapProductDetails($productDetails, $shop->name);
    }

    /**
     * Преобразует массив "сырых" данных продуктов согласно конфигурационному маппингу.
     *
     * @param array  $products Массив продуктов в формате "ключ => значение"
     * @param string $shop     Идентификатор магазина (например, 'start-stop')
     *
     * @return array Преобразованный массив с ключами, соответствующими полям БД
     */

    protected function mapProductDetails(array $products, string $shop): array
    {
        $productConfig = config("product.{$shop}.fields", []);

        return array_map(function ($product) use ($productConfig) {
            $mapped = [];

            foreach ($product as $originalKey => $value) {
                if (isset($productConfig[$originalKey])) {
                    $mappedKey = $productConfig[$originalKey];
                    $mapped[$mappedKey] = $value;
                } else {
                    // Оставляем ключ без изменений, если он отсутствует в конфиге
                    $mapped[$originalKey] = $value;
                }
            }

            return $mapped;
        }, $products);
    }
}
