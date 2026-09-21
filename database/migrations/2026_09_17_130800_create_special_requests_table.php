<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A bespoke ask that doesn't fit the standing menu — a client describes
     * what they want and staff follow up with a quote outside the standard
     * booking flow.
     */
    public function up(): void
    {
        Schema::create('special_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('event_date')->nullable();
            $table->unsignedInteger('guest_count')->nullable();
            $table->string('occasion')->nullable();
            $table->text('details');
            $table->string('budget_range')->nullable();
            $table->enum('status', ['new', 'in_review', 'quoted', 'accepted', 'declined', 'cancelled'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_requests');
    }
};
