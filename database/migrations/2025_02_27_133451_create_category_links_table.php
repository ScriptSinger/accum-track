<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')            // Связь с таблицей магазинов
                ->constrained('shops')              // Создаёт внешний ключ для связи с таблицей shops
                ->onDelete('cascade');              // При удалении магазина удаляются и его категории

            // Внешний ключ для связи с таблицей категорий (categories)
            $table->foreignId('category_id')
                ->constrained('categories');


            $table->string('category_name');        // Название категории
            $table->string('category_url');         // Ссылка на категорию

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_links');
    }
};
