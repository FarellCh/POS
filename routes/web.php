<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCashierSessionController;
use App\Http\Controllers\AdminCashierUserController;
use App\Http\Controllers\AdminPaymentMethodController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KasirDashboardController;
use App\Http\Controllers\KasirOrderController;
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
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/cashier-sessions', [AdminCashierSessionController::class, 'index'])->name('cashier-sessions.index');
        Route::post('/cashier-users', [AdminCashierUserController::class, 'store'])->name('cashier-users.store');
        Route::get('/payment-methods', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::post('/payment-methods', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::post('/payment-methods/store', [AdminPaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    });
});
