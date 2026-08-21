<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500)->index();
            $table->string('page_type', 64)->nullable()->index();
            $table->string('session_id', 128)->nullable()->index();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->unsignedBigInteger('visitable_id')->nullable();
            $table->string('visitable_type', 64)->nullable();
            $table->timestamp('visited_at')->useCurrent();
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->index('visited_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
