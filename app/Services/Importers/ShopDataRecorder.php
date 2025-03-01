<?php

namespace App\Services\Importers;

use App\Models\CategoryLink;
use App\Models\ProductLink;
use App\Models\Shop;

class ShopDataRecorder
{
    public function importProductLinks(array $links, Shop $shop): void
    {
        foreach ($links as $link) {
            ProductLink::firstOrCreate(
                ['url' => $link],
                ['shop_id' => $shop->id]
            );
        }
    }

    /**
     * Импортирует категории в базу данных.
     *
     * @param array $links Массив категорий, где каждая категория имеет ключи 'name' и 'url'
     * @param Shop $shop Экземпляр магазина для связывания категории с магазином
     */
    public function importCategoryLinks(array $links, Shop $shop): void
    {
        foreach ($links as $link) {
            // Проверяем наличие необходимых ключей в массиве
            if (isset($link['name'], $link['url'])) {
                CategoryLink::firstOrCreate(
                    [
                        'category_name' => $link['name'],
                        'shop_id'       => $shop->id,
                    ],
                    [
                        'category_url'  => $link['url'],
                    ]
                );
            }
        }
    }
}
