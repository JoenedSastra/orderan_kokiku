@extends('layouts.app')
@section('title', 'Stock Barang Keluar')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header flex-wrap gap-2">
    <h2 class="h5 mb-0">Stock Barang Keluar</h2>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="kk-search-box">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang...">
        </div>
        <a href="{{ route('kasir.stock_out.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
            <i class="bi bi-plus-lg"></i> Catat Keluar
        </a>
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
                @forelse($stockOuts as $s)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $s->created_at->translatedFormat('l, H:i, d-m-Y') }}</td>
                    <td class="text-center fw-semibold" data-search="nama-barang">{{ $s->item->name }}</td>
                    <td class="text-center">{{ $s->quantity }}</td>
                    <td class="text-center">{{ $s->item->unit }}</td>
                    <td class="text-center">{{ $s->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $s->user->role?->name ?? '?' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada catatan keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $stockOuts->links() }}</div>
</div>
@endsection
