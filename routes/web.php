<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\Master\BranchController;
use App\Http\Controllers\Admin\Master\BrandController;
use App\Http\Controllers\Admin\Master\CategoryController;
use App\Http\Controllers\Admin\Master\CustomerController;
use App\Http\Controllers\Admin\Master\SupplierController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\RepairController;
use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', fn() => view('dashboard'))->name('search');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // User Management
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // Role Management
    Route::middleware('permission:roles.view')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
    });

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
    Route::delete('/activity-log', [ActivityLogController::class, 'destroy'])->name('activity-log.destroy');

    // Master Data
    Route::middleware('permission:branches.view')->group(function () {
        Route::resource('branches', BranchController::class);
    });
    Route::middleware('permission:brands.view')->group(function () {
        Route::resource('brands', BrandController::class);
    });
    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('categories', CategoryController::class);
    });
    Route::middleware('permission:customers.view')->group(function () {
        Route::resource('customers', CustomerController::class);
    });
    Route::middleware('permission:suppliers.view')->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    // Products
    Route::middleware('permission:products.view')->group(function () {
        Route::resource('products', ProductController::class);
        Route::delete('products/{product}/media/{media}', [ProductController::class, 'destroyMedia'])->name('products.media.destroy');
    });

    // Stock Management
    Route::middleware('permission:inventory.view')->group(function () {
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
        Route::get('/stock/movements', [StockController::class, 'movements'])->name('stock.movements');
        Route::get('/stock-adjustments', [StockController::class, 'adjustments'])->name('stock-adjustments.index');
        Route::get('/stock-adjustments/create', [StockController::class, 'adjustmentCreate'])->name('stock-adjustments.create');
        Route::post('/stock-adjustments', [StockController::class, 'adjustmentStore'])->name('stock-adjustments.store');
        Route::get('/stock-transfers', [StockController::class, 'transfers'])->name('stock-transfers.index');
        Route::get('/stock-transfers/create', [StockController::class, 'transferCreate'])->name('stock-transfers.create');
        Route::post('/stock-transfers', [StockController::class, 'transferStore'])->name('stock-transfers.store');
        Route::post('/stock-transfers/{transfer}/receive', [StockController::class, 'transferReceive'])->name('stock-transfers.receive');
    });

    // Purchase Orders
    Route::middleware('permission:purchase.view')->group(function () {
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/purchase-orders/{order}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::post('/purchase-orders/{order}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
        Route::post('/purchase-orders/{order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::post('/purchase-orders/{order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
        Route::post('/purchase-orders/{order}/payment', [PurchaseOrderController::class, 'recordPayment'])->name('purchase-orders.payment');
    });

    // POS System
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search-products', [PosController::class, 'searchProducts'])->name('pos.search-products');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/{id}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/history', [PosController::class, 'history'])->name('pos.history');
    Route::post('/pos/{sale}/cancel', [PosController::class, 'cancel'])->name('pos.cancel');

    // Placeholder routes
    // Sales Management
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/return', [SaleController::class, 'returnSale'])->name('sales.return-form');
    Route::post('/sales/{sale}/return', [SaleController::class, 'storeReturn'])->name('sales.return-store');
    Route::get('/customers/{customer}/ledger', [SaleController::class, 'customerLedger'])->name('sales.customer-ledger');
    Route::post('/customers/{customer}/payment', [SaleController::class, 'recordCustomerPayment'])->name('sales.customer-payment');
    // Quotations
    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
    Route::post('/quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.status');
    Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToSale'])->name('quotations.convert');
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
    // Repairs
    Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/create', [RepairController::class, 'create'])->name('repairs.create');
    Route::post('/repairs', [RepairController::class, 'store'])->name('repairs.store');
    Route::get('/repairs/{repair}', [RepairController::class, 'show'])->name('repairs.show');
    Route::put('/repairs/{repair}', [RepairController::class, 'update'])->name('repairs.update');
    Route::post('/repairs/{repair}/status', [RepairController::class, 'updateStatus'])->name('repairs.status');
    Route::post('/repairs/{repair}/parts', [RepairController::class, 'addPart'])->name('repairs.part.add');
    Route::delete('/repairs/parts/{part}', [RepairController::class, 'removePart'])->name('repairs.part.remove');
    Route::post('/repairs/{repair}/deposit', [RepairController::class, 'recordDeposit'])->name('repairs.deposit');
    // Accounting & Finance
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::get('/accounting/bank-accounts', [AccountingController::class, 'bankAccounts'])->name('accounting.bank-accounts');
    Route::get('/accounting/bank-accounts/create', [AccountingController::class, 'bankAccountCreate'])->name('accounting.bank-accounts.create');
    Route::post('/accounting/bank-accounts', [AccountingController::class, 'bankAccountStore'])->name('accounting.bank-accounts.store');
    Route::get('/accounting/bank-accounts/{bankAccount}/edit', [AccountingController::class, 'bankAccountEdit'])->name('accounting.bank-accounts.edit');
    Route::put('/accounting/bank-accounts/{bankAccount}', [AccountingController::class, 'bankAccountUpdate'])->name('accounting.bank-accounts.update');
    Route::delete('/accounting/bank-accounts/{bankAccount}', [AccountingController::class, 'bankAccountDestroy'])->name('accounting.bank-accounts.destroy');
    Route::get('/accounting/journal-entries', [AccountingController::class, 'journalEntries'])->name('accounting.journal-entries');
    Route::get('/accounting/journal-entries/create', [AccountingController::class, 'journalEntryCreate'])->name('accounting.journal-entries.create');
    Route::post('/accounting/journal-entries', [AccountingController::class, 'journalEntryStore'])->name('accounting.journal-entries.store');
    Route::get('/accounting/journal-entries/{entry}', [AccountingController::class, 'journalEntryShow'])->name('accounting.journal-entry-show');
    Route::post('/accounting/journal-entries/{entry}/post', [AccountingController::class, 'journalEntryPost'])->name('accounting.journal-entry-post');
    Route::post('/accounting/journal-entries/{entry}/void', [AccountingController::class, 'journalEntryVoid'])->name('accounting.journal-entry-void');
    Route::get('/accounting/trial-balance', [AccountingController::class, 'trialBalance'])->name('accounting.trial-balance');
    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::post('/expenses/{expense}/status', [ExpenseController::class, 'updateStatus'])->name('expenses.status');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/categories/all', [ExpenseController::class, 'categories'])->name('expenses.categories');
    Route::post('/expenses/categories', [ExpenseController::class, 'categoryStore'])->name('expenses.category.store');
    Route::put('/expenses/categories/{category}', [ExpenseController::class, 'categoryUpdate'])->name('expenses.category.update');
    Route::delete('/expenses/categories/{category}', [ExpenseController::class, 'categoryDestroy'])->name('expenses.category.destroy');
    Route::get('/reports/sales', fn() => view('dashboard'))->name('reports.sales');
    Route::get('/settings', fn() => view('dashboard'))->name('settings.index');
});

require __DIR__.'/auth.php';
