<?php

namespace App\Services\Importers;

use App\Models\CategoryLink;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductLink;
use App\Models\Shop;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ShopDataRecorder
{
    public function importProductLinks(array $links, Shop $shop): void
    {
        foreach ($links as $link) {
            // Проверяем, что 'url' и 'category_link_id' не пустые
            if (empty($link['url']) || empty($link['category_link_id'])) {
                Log::warning("Пропущена запись: отсутствует URL или category_link_id", $link);
                continue;
            }

            ProductLink::firstOrCreate(
                [
                    'url' => $link['url']
                ],
                [
                    'shop_id' => $shop->id,
                    'category_link_id' => $link['category_link_id']
                ]
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
                        'category_id' => 1
                    ],
                    [
                        'category_url'  => $link['url'],
                    ]
                );
            }
        }
    }


    public function importProducts(array $data)
    {
        foreach ($data as $product) {
            // Отделяем данные о продукте и цене
            $productData = Arr::except($product, ['price']);
            $priceData = $product['price'];

            // Создаем или обновляем продукт
            $productModel = Product::updateOrCreate(
                ['product_link_id' => $productData['product_link_id']],
                $productData
            );

            // Сохраняем цену, привязывая ее к product_id
            Price::updateOrCreate(
                [
                    'product_id' => $productModel->id,
                    'date'       => $priceData['date'] // Цена уникальна на определенную дату
                ],
                [
                    'price'          => $priceData['price'],
                    'trade_in_price' => $priceData['trade_in_price'],
                    'currency'       => $priceData['currency'],
                ]
            );
        }
    }
}
