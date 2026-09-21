<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Originally curated NAMSA Flora's flower catalog/gallery content. Superseded
 * entirely by the Kaleni Catering Services rebrand — SampleMenuItemsSeeder
 * and FoodOfTheDaySeeder now own that job. Left as a no-op (rather than
 * deleted) so environments that already recorded this migration as run keep
 * a consistent migration history.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Intentionally empty — see class docblock.
    }

    public function down(): void
    {
        // Intentionally empty — see class docblock.
    }
};
