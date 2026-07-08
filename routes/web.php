<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\KasirDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KasirDashboardController::class, 'index'])->name('home');

Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [KasirDashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
});
