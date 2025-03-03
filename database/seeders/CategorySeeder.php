<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Сначала создаем категорию с id = 1, которая будет служить как "Нет категории"
        Category::firstOrCreate(
            ['id' => 1],
            ['name' => 'No Category'] // Здесь задаем название категории как "No Category"
        );

        // Затем добавляем другие категории
        $categories = [
            ['name' => 'Грузовые'],
            ['name' => 'Автомобильные'],
            ['name' => 'Мото'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
