@extends('layouts.app')
@section('title', 'Permintaan Barang')
@section('content')
<h2 class="h5 mb-3">Daftar Permintaan Barang</h2>

<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Dari</th><th>Barang</th><th>Jumlah</th><th>Keterangan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ $order->user->role?->name ?? '?' }}</span>
                        {{ $order->user->name }}
                    </td>
                    <td>{{ $order->item->name }}</td>
                    <td>{{ $order->quantity }} {{ $order->item->unit }}</td>
                    <td>{{ $order->keterangan ?? '-' }}</td>
                    <td>{{ $order->created_at->format('d-m-Y') }}</td>
                    <td><span class="kk-badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span></td>
                    <td>
                        @if($order->status === 'menunggu')
                        <form action="{{ route('admin.orders.approve', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi bahwa permintaan ini telah diserahkan/diterima?')">
                                <i class="bi bi-check-lg"></i> Konfirmasi
                            </button>
                        </form>
                        <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak permintaan ini?')">
                                <i class="bi bi-x-lg"></i> Tolak
                            </button>
                        </form>
                        @else
                        <small class="text-muted">
                            Oleh: {{ $order->approvedBy?->name ?? '-' }}<br>
                            {{ $order->approved_at?->format('d-m-Y H:i') }}
                        </small>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Belum ada permintaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
