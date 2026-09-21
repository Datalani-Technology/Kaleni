<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * No customer data depends on these pairings — rebuilt fresh rather than
     * renamed column-by-column, which keeps this migration simple and avoids
     * any doctrine/dbal dependency for in-place column changes.
     */
    public function up(): void
    {
        Schema::dropIfExists('product_recommendations');

        Schema::create('menu_item_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recommended_menu_item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_recommendations');

        Schema::create('product_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('menu_items')->cascadeOnDelete();
            $table->foreignId('recommended_product_id')->constrained('menu_items')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }
};
