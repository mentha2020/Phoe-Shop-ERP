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
use Illuminate\Support\Facades\Route;

// Home redirect
Route::get('/', fn() => redirect()->route('login'));

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Search
    Route::get('/search', fn() => view('dashboard'))->name('search');

    // User Management
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show']);

    // Master Data
    Route::resource('branches', BranchController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Inventory (placeholder routes for Phase 5)
    Route::get('/stock', fn() => view('dashboard'))->name('stock.index');
    Route::get('/stock-transfers', fn() => view('dashboard'))->name('stock-transfers.index');
    Route::get('/stock-adjustments', fn() => view('dashboard'))->name('stock-adjustments.index');

    // Purchasing (placeholder routes for Phase 6)
    Route::get('/purchase-orders', fn() => view('dashboard'))->name('purchase-orders.index');

    // POS (placeholder routes for Phase 7)
    Route::get('/pos', fn() => view('dashboard'))->name('pos.index');

    // Sales (placeholder routes for Phase 8)
    Route::get('/sales', fn() => view('dashboard'))->name('sales.index');
    Route::get('/quotations', fn() => view('dashboard'))->name('quotations.index');

    // Repairs (placeholder routes for Phase 9)
    Route::get('/repairs', fn() => view('dashboard'))->name('repairs.index');

    // Accounting (placeholder routes for Phase 10)
    Route::get('/accounting', fn() => view('dashboard'))->name('accounting.index');
    Route::get('/expenses', fn() => view('dashboard'))->name('expenses.index');

    // Reports (placeholder routes for Phase 11)
    Route::get('/reports/sales', fn() => view('dashboard'))->name('reports.sales');

    // Settings (placeholder routes for Phase 12)
    Route::get('/settings', fn() => view('dashboard'))->name('settings.index');

    // Activity Log
    Route::get('/activity-log', fn() => view('dashboard'))->name('activity-log');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
