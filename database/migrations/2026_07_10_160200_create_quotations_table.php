<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->uuid('customer_id')->nullable();
            $table->string('quotation_number')->unique();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');
            $table->date('valid_until')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('notes')->nullable();
            $table->string('terms')->nullable();
            $table->uuid('converted_sale_id')->nullable();
            $table->uuid('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
