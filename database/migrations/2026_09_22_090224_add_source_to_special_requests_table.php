<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('special_requests', function (Blueprint $table) {
            // Which public form the client used — the two forms ask slightly
            // different questions (a bespoke item vs. a whole event) and now
            // get separate admin boards. Existing rows predate this field and
            // are backfilled to 'special_request_page' below, since that is
            // the original, named feature these came through.
            $table->string('source')->nullable()->after('details');
        });

        DB::table('special_requests')->whereNull('source')->update(['source' => 'special_request_page']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('special_requests', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
