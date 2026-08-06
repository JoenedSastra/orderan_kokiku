<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ==============================
// Guest routes (belum login)
// ==============================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// ==============================
// Authenticated routes
// ==============================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // ---- Admin ----
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // Menu berikut akan diisi bertahap pada fase-fase berikutnya:
        // Master Barang, Kategori, Supplier, Stock Barang, Permintaan Barang,
        // Riwayat, Laporan, User, Role
    });

    // ---- Kasir (Front) ----
    Route::middleware('role:kasir')->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kasir'])->name('dashboard');

        // Menu berikut akan diisi bertahap pada fase-fase berikutnya:
        // Stock Barang Masuk, Stock Barang Keluar, Order Barang, Riwayat, Profil
    });

    // ---- Kitchen (Dapur) ----
    Route::middleware('role:kitchen')->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kitchen'])->name('dashboard');

        // Menu berikut akan diisi bertahap pada fase-fase berikutnya:
        // Stock Barang Masuk, Stock Barang Keluar, Order Barang, Riwayat, Profil
    });
});
