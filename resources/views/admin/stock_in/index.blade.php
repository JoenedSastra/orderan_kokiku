@extends('layouts.app')
@section('title', 'Barang Masuk Harian')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header flex-wrap gap-2">
    <div>
        <h2 class="h5 mb-1">Barang Masuk Harian</h2>
        <p class="text-muted small mb-0">Pilih divisi tujuan, lalu isi barang yang masuk hari ini.</p>
    </div>
    <a href="{{ route('admin.stock_in.riwayat') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-clock-history"></i> Riwayat Hari Ini
    </a>
</div>

<div class="row g-3">
    @foreach($lokasiList as $lokasi)
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.stock_in.create', ['lokasi' => $lokasi['key']]) }}" class="text-decoration-none">
            <div class="kk-stat-card {{ $lokasi['gradient'] }}" style="cursor:pointer;">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="kk-stat-icon"><i class="bi {{ $lokasi['icon'] }}"></i></div>
                </div>
                <div class="kk-stat-value">{{ $lokasi['total'] }}</div>
                <div class="kk-stat-label">{{ $lokasi['label'] }}</div>
                <div class="small mt-1" style="color:rgba(255,255,255,0.85);">
                    <i class="bi bi-plus-circle"></i> Catat barang masuk
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@endsection
