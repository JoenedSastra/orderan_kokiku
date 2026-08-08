@extends('layouts.app')
@section('title', 'Stok Barang')
@section('content')
<div class="kk-stat-card mb-3">
    <h6 class="mb-3">Ringkasan Stok per Barang</h6>

    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#lokasi-gudang">
                <i class="bi bi-building"></i> Gudang Utama
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#lokasi-resto">
                <i class="bi bi-shop"></i> Resto
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#lokasi-kasir">
                <i class="bi bi-cash-coin"></i> Kasir
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#lokasi-kitchen">
                <i class="bi bi-egg-fried"></i> Kitchen
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- ==== GUDANG UTAMA (semua kategori) ==== --}}
        <div class="tab-pane fade show active" id="lokasi-gudang">
            @include('admin.stock.partials.ringkasan-tabel', [
                'categories'   => $categories,
                'locationKey'  => 'gudang',
                'prefix'       => 'gudang',
            ])
        </div>

        {{-- ==== RESTO / TOTAL (semua kategori) ==== --}}
        <div class="tab-pane fade" id="lokasi-resto">
            @include('admin.stock.partials.ringkasan-tabel', [
                'categories'   => $categories,
                'locationKey'  => 'resto',
                'prefix'       => 'resto',
            ])
        </div>

        {{-- ==== KASIR (hanya kategori milik Kasir / Umum) ==== --}}
        <div class="tab-pane fade" id="lokasi-kasir">
            @include('admin.stock.partials.ringkasan-tabel', [
                'categories'   => $categories->filter(fn ($c) => $c->used_by !== 'kitchen')->values(),
                'locationKey'  => 'kasir',
                'prefix'       => 'kasir',
            ])
        </div>

        {{-- ==== KITCHEN (hanya kategori milik Kitchen / Umum) ==== --}}
        <div class="tab-pane fade" id="lokasi-kitchen">
            @include('admin.stock.partials.ringkasan-tabel', [
                'categories'   => $categories->filter(fn ($c) => $c->used_by !== 'kasir')->values(),
                'locationKey'  => 'kitchen',
                'prefix'       => 'kitchen',
            ])
        </div>
    </div>
</div>

<ul class="nav nav-tabs mb-3" id="stockTab">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#masuk">Barang Masuk</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#keluar">Barang Keluar</a></li>
</ul>

<div class="tab-content kk-stat-card">
    {{-- Tab Masuk --}}
    <div class="tab-pane fade show active" id="masuk">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Keterangan</th><th>Oleh</th></tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $s)
                    <tr>
                        <td>{{ $s->tanggal->format('d-m-Y') }}</td>
                        <td>{{ $s->item->name }}</td>
                        <td>{{ $s->quantity }} {{ $s->item->unit }}</td>
                        <td>{{ $s->keterangan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $s->user->role?->name ?? '?' }}</span>
                            {{ $s->user->name }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $stockIns->links() }}</div>
    </div>

    {{-- Tab Keluar --}}
    <div class="tab-pane fade" id="keluar">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Keterangan</th><th>Oleh</th></tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $s)
                    <tr>
                        <td>{{ $s->tanggal->format('d-m-Y') }}</td>
                        <td>{{ $s->item->name }}</td>
                        <td>{{ $s->quantity }} {{ $s->item->unit }}</td>
                        <td>{{ $s->keterangan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $s->user->role?->name ?? '?' }}</span>
                            {{ $s->user->name }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $stockOuts->links() }}</div>
    </div>
</div>
@endsection
