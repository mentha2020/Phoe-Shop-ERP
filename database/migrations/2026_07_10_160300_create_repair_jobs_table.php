<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->uuid('customer_id')->nullable();
            $table->string('ticket_number')->unique();
            $table->enum('status', ['received', 'diagnosed', 'in_progress', 'waiting_parts', 'completed', 'delivered', 'cancelled'])->default('received');
            $table->string('device_type')->nullable();
            $table->string('device_brand')->nullable();
            $table->string('device_model')->nullable();
            $table->string('device_serial')->nullable();
            $table->text('device_password')->nullable();
            $table->text('issue_description');
            $table->text('diagnosis')->nullable();
            $table->text('resolution')->nullable();
            $table->decimal('estimated_cost', 12, 2)->default(0);
            $table->decimal('final_cost', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->date('estimated_completion')->nullable();
            $table->date('actual_completion')->nullable();
            $table->date('delivered_at')->nullable();
            $table->uuid('assigned_to')->nullable();
            $table->uuid('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_jobs');
    }
};
