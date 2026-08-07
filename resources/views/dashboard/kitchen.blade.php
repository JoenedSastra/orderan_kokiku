@extends('layouts.app')
@section('title', 'Dashboard Kitchen')

@section('content')

{{-- Hero Greeting --}}
<div class="kk-hero-card mb-4" style="background: linear-gradient(135deg, #064e3b 0%, #1a1d2e 60%, #022c22 100%);">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="kk-hero-greeting" style="color:rgba(110,231,183,0.7);">🍳 Panel Kitchen</div>
            <div class="kk-hero-name">Halo, {{ $user->name }}!</div>
            <div class="kk-hero-sub">Pantau stok bahan dan catat aktivitas dapur hari ini.</div>
            <div class="kk-hero-badge" style="background:rgba(16,185,129,0.2); border-color:rgba(16,185,129,0.3); color:#6ee7b7;">
                <i class="bi bi-calendar3"></i>
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
        <div class="col-md-4 d-none d-md-flex justify-content-end align-items-center">
            <div style="font-size:5rem; opacity:0.12; line-height:1; position:relative; z-index:1;">🍳</div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('kitchen.orders.create') }}" class="kk-quick-action">
            <i class="bi bi-plus-circle-fill" style="color:var(--kk-orange);"></i>
            <span>Buat Order</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('kitchen.stock_in.index') }}" class="kk-quick-action">
            <i class="bi bi-box-arrow-in-down-right" style="color:var(--kk-success);"></i>
            <span>Catat Masuk</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('kitchen.stock_out.index') }}" class="kk-quick-action">
            <i class="bi bi-box-arrow-up-right" style="color:var(--kk-danger);"></i>
            <span>Catat Keluar</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('kitchen.stock_harian.index') }}" class="kk-quick-action">
            <i class="bi bi-calendar-week-fill" style="color:var(--kk-success);"></i>
            <span>Stock Harian</span>
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="kk-stat-card gradient-red">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            </div>
            <div class="kk-stat-value">{{ $stokRendah }}</div>
            <div class="kk-stat-label">Barang Stok Rendah</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="kk-stat-card gradient-amber">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <div class="kk-stat-value">{{ $permintaanMenunggu }}</div>
            <div class="kk-stat-label">Permintaan Menunggu</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="kk-stat-card gradient-green">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-box-arrow-up-right"></i></div>
            </div>
            <div class="kk-stat-value">{{ $keluarHariIni }}</div>
            <div class="kk-stat-label">Barang Keluar Hari Ini</div>
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="kk-stat-card">
    <div class="kk-section-header">
        <div class="kk-section-title">
            <i class="bi bi-clock-history"></i>
            Permintaan Saya Terbaru
        </div>
        <a href="{{ route('kitchen.orders.index') }}" class="btn btn-sm btn-outline-secondary">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    @if($ordersRecent->isEmpty())
        <div class="text-center py-5">
            <div style="font-size:2.5rem; color:var(--kk-border);"><i class="bi bi-inbox"></i></div>
            <div class="text-muted mt-2" style="font-size:0.875rem;">Belum ada permintaan.</div>
            <a href="{{ route('kitchen.orders.create') }}" class="btn btn-sm btn-primary mt-3">
                <i class="bi bi-plus me-1"></i>Buat Permintaan Pertama
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Barang</th><th>Jumlah</th><th>Status</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                    @foreach($ordersRecent as $order)
                    <tr>
                        <td class="fw-semibold" style="font-size:0.875rem;">{{ $order->item->name }}</td>
                        <td style="font-size:0.875rem;">{{ $order->quantity }} {{ $order->item->unit }}</td>
                        <td><span class="kk-badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span></td>
                        <td class="text-muted" style="font-size:0.8rem;">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
