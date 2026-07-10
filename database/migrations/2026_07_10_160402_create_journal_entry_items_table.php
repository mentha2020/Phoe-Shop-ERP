<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entry_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_entry_id');
            $table->string('account_code');
            $table->string('account_name');
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->string('description')->nullable();
            $table->uuid('bank_account_id')->nullable();
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->cascadeOnDelete();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_items');
    }
};
