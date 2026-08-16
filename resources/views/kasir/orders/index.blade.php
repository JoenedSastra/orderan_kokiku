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
                <tr><th>#</th><th>Nama Barang</th><th>Jumlah</th><th>Keterangan</th><th>Tanggal</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $loop->iteration + $orders->firstItem() - 1 }}</td>
                    <td>{{ $order->item->name }}</td>
                    <td>{{ $order->quantity }} {{ $order->item->unit }}</td>
                    <td>{{ $order->keterangan ?? '-' }}</td>
                    <td>{{ $order->created_at->format('d-m-Y') }}</td>
                    <td>
                        @if($order->status === 'disetujui')
                            <span class="badge bg-success">Sudah Dikonfirmasi</span>
                        @elseif($order->status === 'ditolak')
                            <span class="badge bg-danger">Ditolak</span>
                        @else
                            <span class="badge bg-warning text-dark">Belum Dikonfirmasi</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Belum ada permintaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
