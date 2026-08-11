@extends('layouts.app')
@section('title', 'Stock Barang Masuk')
@section('content')
<div class="mb-3 kk-page-header">
    <h2 class="h5 mb-0">Stock Barang Masuk</h2>
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
                @forelse($stockIns as $s)
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
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada catatan masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $stockIns->links() }}</div>
</div>
@endsection
