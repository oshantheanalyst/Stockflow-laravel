<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('description')->default('');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('bill_date');
            $table->date('due_date')->nullable();
            $table->string('method')->default('');
            $table->string('reference')->nullable();
            $table->string('status')->default('Pending'); // Pending, Paid
            $table->date('paid_on')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
