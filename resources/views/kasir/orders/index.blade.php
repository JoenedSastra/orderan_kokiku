@extends('layouts.app')
@section('title', 'Order Barang')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Permintaan Barang Saya</h2>
    <a href="{{ route('kasir.orders.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Buat Permintaan
    </a>
</div>
<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Barang</th><th>Jumlah</th><th>Keterangan</th><th>Status</th><th>Diproses Oleh</th><th>Tanggal</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->item->name }}</td>
                    <td>{{ $order->quantity }} {{ $order->item->unit }}</td>
                    <td>{{ $order->keterangan ?? '-' }}</td>
                    <td><span class="kk-badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span></td>
                    <td>{{ $order->approvedBy?->name ?? '-' }}</td>
                    <td>{{ $order->created_at->format('d-m-Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada permintaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
