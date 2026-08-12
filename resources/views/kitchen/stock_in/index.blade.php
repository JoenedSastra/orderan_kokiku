@extends('layouts.app')
@section('title', 'Stock Barang Masuk')
@section('content')
<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2 class="h5 mb-0">Stock Barang Masuk</h2>
    <div class="kk-search-box">
        <i class="bi bi-search"></i>
        <input type="text" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang...">
    </div>
</div>
<div class="kk-stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Hari, Jam &amp; Tanggal</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockIns as $s)
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center">{{ $s->created_at->translatedFormat('l, H:i, d-m-Y') }}</td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $s->item->name }}</td>
                    <td class="text-center">{{ $s->quantity }}</td>
                    <td class="text-center">{{ $s->item->unit }}</td>
                    <td class="text-center">{{ $s->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $s->user->role?->name ?? '?' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada catatan masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $stockIns->links() }}</div>
</div>
@endsection
