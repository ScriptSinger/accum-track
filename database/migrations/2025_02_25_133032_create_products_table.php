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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->string('name');
            $table->string('voltage')->nullable();
            $table->string('capacity')->nullable();
            $table->string('cca')->nullable();
            $table->string('polarity')->nullable();
            $table->string('terminal_type')->nullable();
            $table->boolean('bottom_fixation')->nullable();
            $table->string('size_standard')->nullable();
            $table->string('technology')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('origin')->nullable();
            $table->string('brand')->nullable();
            $table->string('country')->nullable();
            $table->string('serviceable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
