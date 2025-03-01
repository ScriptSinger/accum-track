<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shop::create([
            'name' => 'start-stop',
            'url' => 'http://start-stop.su',
        ]);

        Shop::create([
            'name' => 'akb-ms',
            'url' => 'http://akb-ms.ru',
        ]); //Баязита Бикбая 2/2

        Shop::create([
            'name' => 'Автомотив',
            'url' => 'http://avtomotiv.ru/ufa/',
        ]);

        Shop::create([
            'name' => 'Мир аккумуляторов',
            'url' => 'http://ufa-akb.ru',
        ]);

        // Shop::create([
        //     'name' => 'Аккумуляторы РФ',
        //     'url' => 'http://xn--80a1bd.xn--80auaeenoogb6gyb.xn--p1ai/',
        // ]);

        // Shop::create([
        //     'name' => 'Акб Мастер',
        //     'url' => 'http://xn--102-5cdaf3a1bt6bjm.xn--p1ai/',
        // ]);

        // Shop::create([
        //     'name' => 'Акб Авто',
        //     'url' => 'http://akb-ufa.ru',
        // ]);


        // Shop::create([
        //     'name' => 'Автостарт',
        //     'url' => 'http://akb02.ru',
        // ]);

        // Shop::create([
        //     'name' => 'Battery Shop',
        //     'url' => 'http://battery-shop.pro',
        // ]);

        // Shop::create([
        //     'name' => 'Цефей',
        //     'url' => 'http://ufa.cephey.ru',
        // ]);

        // Shop::create([
        //     'name' => 'Центр аккумуляторов',
        //     'url' => 'http://centrakb-ufa.ru',
        // ]);

        // Shop::create([
        //     'name' => 'Акб Сервис',
        //     'url' => 'http://al-akb.ru',
        // ]);

        // Shop::create([
        //     'name' => 'Колеса Даром',
        //     'url' => 'https://ufa.kolesa-darom.ru',
        // ]);

        // Shop::create([
        //     'name' => 'Shinservice',
        //     'url' => 'https://ufa.shinservice.ru',
        // ]);
    }
}
