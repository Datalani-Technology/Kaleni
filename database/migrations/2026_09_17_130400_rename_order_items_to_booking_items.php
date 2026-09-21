<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rebuilt as a drop+create rather than a rename+alter: after the
     * preceding table rename, SQLite's native ALTER TABLE ... DROP COLUMN
     * refuses to drop a column that's still part of a foreign key
     * definition on this table (a hard DDL-level restriction, not just FK
     * enforcement — disabling FK checks doesn't help). No real booking data
     * exists yet to preserve, so a clean rebuild sidesteps it entirely and
     * works identically on MySQL and SQLite.
     */
    public function up(): void
    {
        Schema::dropIfExists('order_items');

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('menu_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }
};
