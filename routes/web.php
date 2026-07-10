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
    Route::get('/quotations', fn() => view('dashboard'))->name('quotations.index');
    Route::get('/repairs', fn() => view('dashboard'))->name('repairs.index');
    Route::get('/accounting', fn() => view('dashboard'))->name('accounting.index');
    Route::get('/expenses', fn() => view('dashboard'))->name('expenses.index');
    Route::get('/reports/sales', fn() => view('dashboard'))->name('reports.sales');
    Route::get('/settings', fn() => view('dashboard'))->name('settings.index');
});

require __DIR__.'/auth.php';
