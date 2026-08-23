<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->nullOnDelete();
        });

        // Backfill: one Customer per distinct order email, using their most recent order's details.
        $rows = DB::table('orders')
            ->select('customer_email', 'customer_name', 'customer_phone', 'delivery_address', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('customer_email');

        foreach ($rows as $email => $orders) {
            if (!$email) {
                continue;
            }
            $latest = $orders->first();
            $customerId = DB::table('customers')->insertGetId([
                'name' => $latest->customer_name,
                'email' => $email,
                'phone' => $latest->customer_phone,
                'address' => $latest->delivery_address,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('orders')->where('customer_email', $email)->update(['customer_id' => $customerId]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
        Schema::dropIfExists('customers');
    }
};
