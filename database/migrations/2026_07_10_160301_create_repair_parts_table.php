<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('repair_job_id');
            $table->uuid('product_id')->nullable();
            $table->string('part_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('repair_job_id')->references('id')->on('repair_jobs')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_parts');
    }
};
