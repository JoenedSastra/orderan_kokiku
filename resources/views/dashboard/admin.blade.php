@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<p class="text-muted mb-4">Selamat datang, {{ $user->name }}. Ringkasan berikut akan terisi otomatis setelah modul Stok & Permintaan Barang dibangun.</p>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">0</div>
                <div class="kk-stat-label">Permintaan Menunggu</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-hourglass-split"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">0</div>
                <div class="kk-stat-label">Barang Stok Rendah</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">0</div>
                <div class="kk-stat-label">Total Supplier</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-truck"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">0</div>
                <div class="kk-stat-label">Total User Aktif</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-people"></i></div>
        </div>
    </div>
</div>

<div class="kk-stat-card">
    <div class="fw-semibold mb-2">Permintaan Barang Terbaru</div>
    <p class="text-muted mb-0" style="font-size: 0.88rem;">Belum ada data &mdash; tabel ini akan menampilkan daftar permintaan dari Kasir &amp; Kitchen setelah modul Permintaan Barang dibangun (Fase 4).</p>
</div>
@endsection
