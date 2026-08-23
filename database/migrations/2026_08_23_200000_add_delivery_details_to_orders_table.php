<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('customer_phone');
            $table->string('recipient_phone', 30)->nullable()->after('recipient_name');
            $table->date('delivery_date')->nullable()->after('delivery_address');
            $table->string('delivery_window', 30)->nullable()->after('delivery_date');
            $table->text('gift_message')->nullable()->after('delivery_window');
            $table->text('delivery_instructions')->nullable()->after('gift_message');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_phone',
                'delivery_date',
                'delivery_window',
                'gift_message',
                'delivery_instructions',
            ]);
        });
    }
};
