@extends('layouts.app')
@section('title', 'Stock Barang Keluar')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Stock Barang Keluar</h2>
    <a href="{{ route('kitchen.stock_out.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Catat Keluar
    </a>
</div>
<div class="kk-stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Hari, Jam &amp; Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOuts as $s)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->created_at->translatedFormat('l, H:i, d-m-Y') }}</td>
                    <td class="fw-semibold">{{ $s->item->name }}</td>
                    <td>{{ $s->quantity }}</td>
                    <td>{{ $s->item->unit }}</td>
                    <td>{{ $s->keterangan ?? '-' }}</td>
                    <td>
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
