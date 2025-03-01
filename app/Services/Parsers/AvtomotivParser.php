<?php

namespace App\Services\Parsers;

use App\Interfaces\ShopParserInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class AvtomotivParser
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Парсит информацию о продукте с переданного URL
     */
    public function scrape(string $url): array
    {
        $response = $this->client->get($url);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        return [
            'title' => $crawler->filter('h1')->text(),
            'price' => $crawler->filter('.price')->text(),
        ];
    }

    /**
     * Парсит ссылки на категории с переданного URL
     */
    public function scrapeCategories(string $url, string $pattern): array
    {
        $response = $this->client->get($url);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        $categories = [];

        // Ищем все ссылки, соответствующие паттерну
        $crawler->filter('a')->each(function (Crawler $node) use ($pattern, &$categories) {
            $link = $node->link()->getUri();

            // Если ссылка соответствует паттерну, добавляем в массив
            if (preg_match($pattern, $link)) {
                $categories[] = [
                    'category_name' => $node->text(),
                    'category_url' => $link,
                ];
            }
        });

        return $categories;
    }
}
