@extends('layouts.app')

@section('title', 'Dashboard Kitchen')

@section('content')
<p class="text-muted mb-4">Selamat datang, {{ $user->name }}. Ringkasan stok &amp; permintaan Anda akan tampil di sini setelah modul Stok Barang dibangun.</p>

<div class="row g-3">
    <div class="col-6 col-lg-4">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">0</div>
                <div class="kk-stat-label">Barang Stok Rendah</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">0</div>
                <div class="kk-stat-label">Permintaan Menunggu</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-hourglass-split"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">0</div>
                <div class="kk-stat-label">Barang Keluar Hari Ini</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-box-arrow-up"></i></div>
        </div>
    </div>
</div>
@endsection
