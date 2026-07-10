<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_id');
            $table->uuid('product_id');
            $table->uuid('product_variant_id')->nullable();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('quotation_id')->references('id')->on('quotations')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
