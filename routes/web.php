<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\StockInController as AdminStockInController;
use App\Http\Controllers\Admin\StockKitchenReportController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Kasir\OrderController as KasirOrderController;
use App\Http\Controllers\Kasir\StockInController as KasirStockInController;
use App\Http\Controllers\Kasir\StockOutController as KasirStockOutController;
use App\Http\Controllers\Kitchen\OrderController as KitchenOrderController;
use App\Http\Controllers\Kitchen\StockInController as KitchenStockInController;
use App\Http\Controllers\Kitchen\StockHarianController;
use App\Http\Controllers\Kitchen\StockOutController as KitchenStockOutController;
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
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // ---- Admin ----
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
        Route::get('/dashboard/chart-data-harian', [DashboardController::class, 'chartDataHarian'])->name('dashboard.chart-data-harian');
        Route::get('/dashboard/chart-data-tahunan', [DashboardController::class, 'chartDataTahunan'])->name('dashboard.chart-data-tahunan');
        Route::get('/dashboard/division-stock', [DashboardController::class, 'divisionStockData'])->name('dashboard.division-stock');

        // Master Data
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers',  SupplierController::class);

        // Stock & Permintaan
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/kirim-barang', [StockController::class, 'kirimBarang'])->name('stock.kirim');
        Route::get('/stock/riwayat-terkirim', [StockController::class, 'riwayatTerkirim'])->name('stock.riwayat_terkirim');
        Route::get('/stock-kasir-kitchen', [StockController::class, 'kasirKitchen'])->name('stock_kasir_kitchen.index');
        Route::get('/stock-kitchen', [StockKitchenReportController::class, 'index'])->name('stock_kitchen.index');

        // Barang Masuk Harian (admin) — pilih divisi dulu, baru isi tabel massal
        Route::get('/stock-masuk',                 [AdminStockInController::class, 'index'])->name('stock_in.index');
        Route::get('/stock-masuk/riwayat',          [AdminStockInController::class, 'riwayat'])->name('stock_in.riwayat');
        Route::get('/stock-masuk/tambah/{lokasi}',  [AdminStockInController::class, 'create'])->name('stock_in.create')
            ->whereIn('lokasi', ['gudang_utama', 'gudang_resto', 'kasir', 'kitchen']);
        Route::post('/stock-masuk/{lokasi}',        [AdminStockInController::class, 'store'])->name('stock_in.store')
            ->whereIn('lokasi', ['gudang_utama', 'gudang_resto', 'kasir', 'kitchen']);
        Route::delete('/stock-masuk/hapus-massal',  [AdminStockInController::class, 'destroyBulk'])->name('stock_in.destroy_bulk');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject',  [AdminOrderController::class, 'reject'])->name('orders.reject');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        // Laporan
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/laporan/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    });

    // ---- Kasir (Front) ----
    Route::middleware('role:kasir')->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kasir'])->name('dashboard');

        Route::get('/stock-masuk', [KasirStockInController::class, 'index'])->name('stock_in.index');

        Route::get('/stock-keluar',        [KasirStockOutController::class, 'index'])->name('stock_out.index');
        Route::get('/stock-keluar/tambah', [KasirStockOutController::class, 'create'])->name('stock_out.create');
        Route::post('/stock-keluar',       [KasirStockOutController::class, 'store'])->name('stock_out.store');

        Route::get('/orders',        [KasirOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/tambah', [KasirOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders',       [KasirOrderController::class, 'store'])->name('orders.store');
    });

    // ---- Kitchen (Dapur) ----
    Route::middleware('role:kitchen')->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kitchen'])->name('dashboard');
        Route::get('/stock-harian', [StockHarianController::class, 'index'])->name('stock_harian.index');

        Route::get('/stock-masuk', [KitchenStockInController::class, 'index'])->name('stock_in.index');

        Route::get('/stock-keluar',        [KitchenStockOutController::class, 'index'])->name('stock_out.index');
        Route::get('/stock-keluar/tambah', [KitchenStockOutController::class, 'create'])->name('stock_out.create');
        Route::post('/stock-keluar',       [KitchenStockOutController::class, 'store'])->name('stock_out.store');

        Route::get('/orders',        [KitchenOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/tambah', [KitchenOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders',       [KitchenOrderController::class, 'store'])->name('orders.store');
    });
});
