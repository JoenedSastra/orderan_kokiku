@extends('layouts.app')
@section('title', 'Stock Barang')
@section('content')
<h2 class="h5 mb-3">Stock Barang — Semua Lokasi</h2>

<div class="kk-stat-card mb-3">
    <h6 class="mb-3">Ringkasan Stok per Barang</h6>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr><th>Barang</th><th>Kategori</th><th>Satuan</th><th class="text-end">Stok Gudang (Admin)</th><th class="text-end">Stok Restoran (Kasir+Kitchen)</th></tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-end">
                        <span class="badge {{ $item->stokGudang() <= $item->min_stock ? 'bg-danger' : 'bg-primary' }}">
                            {{ $item->stokGudang() }}
                        </span>
                    </td>
                    <td class="text-end">
                        <span class="badge {{ $item->stokRestoran() <= $item->min_stock ? 'bg-danger' : 'bg-success' }}">
                            {{ $item->stokRestoran() }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada barang.</td></tr>
                @endforelse
            </tbody>
        </table>
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
