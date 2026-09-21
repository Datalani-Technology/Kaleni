<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Precise scheduling on top of the existing event_date/serving_period:
     * a preferred start time, how the client wants duration measured
     * (a few hours / the whole day / spanning multiple days), and the two
     * fields that only apply to their matching mode.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('serving_period');
            $table->string('duration_mode', 20)->default('full_day')->after('start_time');
            $table->unsignedTinyInteger('duration_hours')->nullable()->after('duration_mode');
            $table->date('end_date')->nullable()->after('duration_hours');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'duration_mode', 'duration_hours', 'end_date']);
        });
    }
};
