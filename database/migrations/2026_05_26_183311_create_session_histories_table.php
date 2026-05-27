<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('session_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('token_id')->nullable(); // Sanctum token ID (null if token already deleted)
            $table->string('device_name')->default('Unknown');   // parsed browser/platform
            $table->string('user_agent', 512)->nullable();       // raw User-Agent string
            $table->string('ip_address', 45)->nullable();
            $table->string('status')->default('active');          // active, revoked, expired
            $table->timestamp('logged_in_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('session_histories');
    }
};
