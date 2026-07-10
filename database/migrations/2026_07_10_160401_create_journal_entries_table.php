<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entry_number')->unique();
            $table->date('entry_date');
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->enum('type', ['general', 'payment', 'receipt', 'journal', 'adjustment'])->default('general');
            $table->string('description');
            $table->decimal('total_debit', 12, 2)->default(0);
            $table->decimal('total_credit', 12, 2)->default(0);
            $table->enum('status', ['draft', 'posted', 'voided'])->default('draft');
            $table->uuid('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
