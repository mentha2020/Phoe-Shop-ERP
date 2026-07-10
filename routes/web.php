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

    // User Management - requires users.view permission
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // Role Management - requires roles.view permission
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
    });

    // Placeholder routes
    Route::get('/stock', fn() => view('dashboard'))->name('stock.index');
    Route::get('/stock-transfers', fn() => view('dashboard'))->name('stock-transfers.index');
    Route::get('/stock-adjustments', fn() => view('dashboard'))->name('stock-adjustments.index');
    Route::get('/purchase-orders', fn() => view('dashboard'))->name('purchase-orders.index');
    Route::get('/pos', fn() => view('dashboard'))->name('pos.index');
    Route::get('/sales', fn() => view('dashboard'))->name('sales.index');
    Route::get('/quotations', fn() => view('dashboard'))->name('quotations.index');
    Route::get('/repairs', fn() => view('dashboard'))->name('repairs.index');
    Route::get('/accounting', fn() => view('dashboard'))->name('accounting.index');
    Route::get('/expenses', fn() => view('dashboard'))->name('expenses.index');
    Route::get('/reports/sales', fn() => view('dashboard'))->name('reports.sales');
    Route::get('/settings', fn() => view('dashboard'))->name('settings.index');
});

require __DIR__.'/auth.php';
