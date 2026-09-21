<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('promo_code_product');

        Schema::create('promo_code_menu_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->unique(['promo_code_id', 'menu_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_menu_item');

        Schema::create('promo_code_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('menu_items')->cascadeOnDelete();
            $table->unique(['promo_code_id', 'product_id']);
        });
    }
};
