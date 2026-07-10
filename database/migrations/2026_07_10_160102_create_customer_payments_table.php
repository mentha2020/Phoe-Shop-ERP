<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('sale_id')->nullable();
            $table->string('reference_number')->unique();
            $table->enum('type', ['payment', 'credit', 'refund', 'adjustment'])->default('payment');
            $table->decimal('amount', 12, 2);
            $table->string('method', 50)->default('cash');
            $table->string('notes')->nullable();
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('sale_id')->references('id')->on('sales')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
