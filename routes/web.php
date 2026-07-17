<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCashierSessionController;
use App\Http\Controllers\AdminCashierUserController;
use App\Http\Controllers\AdminInventoryController;
use App\Http\Controllers\AdminPaymentMethodController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KasirDashboardController;
use App\Http\Controllers\KasirOrderController;
use App\Http\Controllers\KasirTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [KasirDashboardController::class, 'index'])->name('home');

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirDashboardController::class, 'index'])->name('dashboard');
        Route::post('/confirm-item', [KasirOrderController::class, 'store'])->name('confirm-item');
        Route::post('/transactions', [KasirTransactionController::class, 'store'])->name('transactions.store');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/suppliers', [AdminInventoryController::class, 'storeSupplier'])->name('inventory.suppliers.store');
        Route::post('/inventory/purchases', [AdminInventoryController::class, 'storePurchase'])->name('inventory.purchases.store');
        Route::post('/inventory/opname', [AdminInventoryController::class, 'storeOpname'])->name('inventory.opname.store');
        Route::post('/inventory/damage', [AdminInventoryController::class, 'storeDamage'])->name('inventory.damage.store');
        Route::get('/cashier-sessions', [AdminCashierSessionController::class, 'index'])->name('cashier-sessions.index');
        Route::post('/cashier-users', [AdminCashierUserController::class, 'store'])->name('cashier-users.store');
        Route::get('/payment-methods', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::post('/payment-methods', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::post('/payment-methods/store', [AdminPaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::post('/transactions/{transaction}/void', [AdminTransactionController::class, 'void'])->name('transactions.void');
    });
});
