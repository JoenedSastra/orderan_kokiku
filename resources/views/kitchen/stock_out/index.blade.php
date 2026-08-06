@extends('layouts.app')
@section('title', 'Stock Barang Keluar')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Stock Barang Keluar</h2>
    <a href="{{ route('kitchen.stock_out.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Catat Keluar
    </a>
</div>
<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                @forelse($stockOuts as $s)
                <tr>
                    <td>{{ $s->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $s->item->name }}</td>
                    <td>{{ $s->quantity }} {{ $s->item->unit }}</td>
                    <td>{{ $s->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada catatan keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $stockOuts->links() }}</div>
</div>
@endsection
