<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a request cover a span of days (e.g. an office's lunch for a
     * week) instead of only a single date — event_date becomes the start
     * date when is_recurring is true.
     */
    public function up(): void
    {
        Schema::table('special_requests', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('event_date');
            $table->date('recurring_end_date')->nullable()->after('is_recurring');
        });
    }

    public function down(): void
    {
        Schema::table('special_requests', function (Blueprint $table) {
            $table->dropColumn(['is_recurring', 'recurring_end_date']);
        });
    }
};
