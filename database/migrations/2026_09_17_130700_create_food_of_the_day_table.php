<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One scheduled "special" per calendar date. It can either spotlight an
     * existing menu item (menu_item_id set, overrides left null) or be a
     * one-off dish that isn't on the standing menu at all (menu_item_id
     * null, title/description/price filled in directly).
     */
    public function up(): void
    {
        Schema::create('food_of_the_day', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->date('serve_date')->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_of_the_day');
    }
};
