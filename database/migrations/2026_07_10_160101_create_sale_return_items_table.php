<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_return_id');
            $table->uuid('sale_item_id');
            $table->uuid('product_id');
            $table->uuid('product_variant_id')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->cascadeOnDelete();
            $table->foreign('sale_item_id')->references('id')->on('sale_items');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_return_items');
    }
};
