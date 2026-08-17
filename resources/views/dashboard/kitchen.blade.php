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

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6">
        <div class="kk-stat-card gradient-red">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            </div>
            <div class="kk-stat-value">{{ $stokRendah }}</div>
            <div class="kk-stat-label">Barang Stok Rendah</div>
        </div>
    </div>
    <div class="col-6">
        <div class="kk-stat-card gradient-green">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-box-arrow-up-right"></i></div>
            </div>
            <div class="kk-stat-value">{{ $keluarHariIni }}</div>
            <div class="kk-stat-label">Barang Keluar Hari Ini</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Aksi Cepat --}}
    <div class="col-lg-6">
        <div class="kk-stat-card h-100">
            <div class="kk-section-header mb-3">
                <div class="kk-section-title">
                    <i class="bi bi-lightning-charge"></i> Aksi Cepat
                </div>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('kitchen.orders.create') }}" class="kk-quick-action">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Buat Order</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('kitchen.stock_in.index') }}" class="kk-quick-action">
                        <i class="bi bi-box-arrow-in-down-right"></i>
                        <span>Barang Masuk</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('kitchen.stock_out.index') }}" class="kk-quick-action">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Barang Keluar</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('kitchen.stock_harian.index') }}" class="kk-quick-action">
                        <i class="bi bi-calendar-week-fill"></i>
                        <span>Stock Harian</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stok Rendah Kitchen --}}
    <div class="col-lg-6">
        <div class="kk-stat-card h-100 flex-fill">
            <div class="kk-section-header mb-3">
                <div class="kk-section-title text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Rendah
                </div>
                <span class="badge bg-danger rounded-pill">{{ $stokRendah }}</span>
            </div>
            @if($stokRendah > 0)
                <div class="d-flex flex-column flex-md-row gap-3 flex-wrap">
                    @foreach(\App\Models\Item::where('master_location', \App\Models\Item::MASTER_KITCHEN)->get()->filter(fn($i) => $i->stokKitchen() <= 10)->take(5) as $item)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded flex-fill" style="background: var(--kk-danger-soft); border: 1px solid rgba(239, 68, 68, 0.2);">
                            <div>
                                <div class="fw-semibold" style="font-size:0.95rem; color:var(--kk-text);">{{ $item->name }}</div>
                                <div style="font-size:0.85rem; color:var(--kk-danger);">Sisa: {{ $item->stokKitchen() }} {{ $item->unit }}</div>
                            </div>
                            <a href="{{ route('kitchen.orders.create', ['item_id' => $item->id]) }}" class="btn btn-sm btn-outline-danger" style="font-weight: 500;">Restock</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted" style="font-size:0.95rem;">
                    <i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>
                    Stok kitchen aman.
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
