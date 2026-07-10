<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->index('branch_id');
                $table->index('customer_id');
                $table->index('status');
                $table->index('created_at');
                $table->index('created_by');
            });
        }

        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->index('sale_id');
                $table->index('product_id');
            });
        }

        if (Schema::hasTable('sale_payments')) {
            Schema::table('sale_payments', function (Blueprint $table) {
                $table->index('sale_id');
            });
        }

        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->index('branch_id');
                $table->index('supplier_id');
                $table->index('status');
                $table->index('created_at');
            });
        }

        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->index('purchase_order_id');
                $table->index('product_id');
            });
        }

        if (Schema::hasTable('stocks')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->index(['branch_id', 'product_id']);
                $table->index('quantity');
            });
        }

        if (Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->index('branch_id');
                $table->index('product_id');
                $table->index('type');
                $table->index('created_at');
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('category_id');
                $table->index('brand_id');
                $table->index('is_active');
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->index('expense_category_id');
                $table->index('branch_id');
                $table->index('status');
                $table->index('expense_date');
            });
        }

        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->index('name');
                $table->index('phone');
            });
        }

        if (Schema::hasTable('repair_jobs')) {
            Schema::table('repair_jobs', function (Blueprint $table) {
                $table->index('branch_id');
                $table->index('customer_id');
                $table->index('status');
                $table->index('assigned_to');
            });
        }

        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->index('branch_id');
                $table->index('customer_id');
                $table->index('status');
            });
        }

        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->index('status');
                $table->index('entry_date');
                $table->index('type');
            });
        }

        if (Schema::hasTable('journal_entry_items')) {
            Schema::table('journal_entry_items', function (Blueprint $table) {
                $table->index('journal_entry_id');
                $table->index('account_code');
                $table->index('bank_account_id');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
