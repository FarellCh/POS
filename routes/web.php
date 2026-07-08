<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KasirDashboardController;
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
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    });
});
