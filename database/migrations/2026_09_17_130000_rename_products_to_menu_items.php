<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Products (flowers) become menu items (catering dishes/packages).
     */
    public function up(): void
    {
        Schema::rename('products', 'menu_items');

        Schema::table('menu_items', function (Blueprint $table) {
            // e.g. "per person", "per tray", "per pack", "each" — catering is
            // rarely priced as a single flat unit the way a bouquet was.
            $table->string('unit_label')->nullable()->after('price');
            // e.g. "serves 10" for platters/packages.
            $table->unsignedInteger('serves_count')->nullable()->after('unit_label');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['unit_label', 'serves_count']);
        });

        Schema::rename('menu_items', 'products');
    }
};
