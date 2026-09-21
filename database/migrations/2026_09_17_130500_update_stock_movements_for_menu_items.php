<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite refuses to drop a column that's still part of a foreign key
        // definition, so disable FK enforcement around the drop (no-op on
        // MySQL, which handles the combined DROP FOREIGN KEY/COLUMN natively).
        Schema::disableForeignKeyConstraints();
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
        Schema::enableForeignKeyConstraints();

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('menu_item_id')->after('id')->constrained('menu_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropColumn('menu_item_id');
        });
        Schema::enableForeignKeyConstraints();

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('product_id')->after('id')->constrained('menu_items')->cascadeOnDelete();
        });
    }
};
