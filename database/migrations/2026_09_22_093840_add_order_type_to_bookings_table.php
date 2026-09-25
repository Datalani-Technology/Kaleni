<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distinguishes a full catering event booking from a quick food order
     * placed without one — both share this same table/workflow, only the
     * public checkout form and admin board differ per type. fulfillment_method
     * (pickup/delivery) only applies to quick orders; event_address stays
     * NOT NULL and is populated with a "pickup" placeholder for quick orders
     * that don't need delivery, avoiding a doctrine/dbal column-change.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('order_type', 20)->default('catering_booking')->after('booking_number');
            $table->string('fulfillment_method', 20)->nullable()->after('order_type');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'fulfillment_method']);
        });
    }
};
