<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id');
            $table->string('reference_number')->unique();
            $table->enum('method', ['cash', 'card', 'transfer', 'e_wallet', 'credit', 'mixed'])->default('cash');
            $table->decimal('amount', 12, 2);
            $table->string('card_number')->nullable();
            $table->string('card_type')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('e_wallet_type')->nullable();
            $table->string('notes')->nullable();
            $table->uuid('received_by');
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
            $table->foreign('received_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
