<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Orders (flower deliveries) become bookings (catering for an event).
     * Column types are preserved — only names/semantics change — so most of
     * this is native RENAME COLUMN calls. The one enum column (order_status)
     * is swapped via drop+add instead: MySQL's native RENAME COLUMN mangles
     * the quoted default on enum columns (produces an invalid triple-quoted
     * default and fails), so renameColumn() can't be used for it here.
     */
    public function up(): void
    {
        Schema::rename('orders', 'bookings');

        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('order_number', 'booking_number');
            $table->renameColumn('recipient_name', 'onsite_contact_name');
            $table->renameColumn('recipient_phone', 'onsite_contact_phone');
            $table->renameColumn('delivery_address', 'event_address');
            $table->renameColumn('delivery_date', 'event_date');
            $table->renameColumn('delivery_window', 'serving_period');
            $table->renameColumn('gift_message', 'special_message');
            $table->renameColumn('delivery_instructions', 'event_notes');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('order_status');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending')->after('discount_amount');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('guest_count')->nullable()->after('serving_period');
            $table->string('event_type')->nullable()->after('guest_count');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_count', 'event_type', 'booking_status']);
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('order_status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending')->after('discount_amount');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('booking_number', 'order_number');
            $table->renameColumn('onsite_contact_name', 'recipient_name');
            $table->renameColumn('onsite_contact_phone', 'recipient_phone');
            $table->renameColumn('event_address', 'delivery_address');
            $table->renameColumn('event_date', 'delivery_date');
            $table->renameColumn('serving_period', 'delivery_window');
            $table->renameColumn('special_message', 'gift_message');
            $table->renameColumn('event_notes', 'delivery_instructions');
        });

        Schema::rename('bookings', 'orders');
    }
};
