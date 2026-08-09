@extends('layouts.app')
@section('title', 'Barang Masuk Gudang')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Barang Masuk Gudang</h2>
    <a href="{{ route('admin.stock_in.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Catat Masuk
    </a>
</div>
<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Supplier</th><th>Master Barang</th><th>Keterangan</th><th>Dicatat Oleh</th></tr>
            </thead>
            <tbody>
                @forelse($stockIns as $s)
                <tr>
                    <td>{{ $s->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $s->item->name }}</td>
                    <td>{{ $s->quantity }} {{ $s->item->unit }}</td>
                    <td>{{ $s->supplier->name ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $s->item->masterLocationLabel() }}</span></td>
                    <td>
                        {{ $s->keterangan ?? '-' }}
                        @if($s->is_completed)
                            <i class="bi bi-check-circle-fill text-success ms-1" title="Selesai"></i>
                        @endif
                    </td>
                    <td>{{ $s->user->name }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada catatan barang masuk gudang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $stockIns->links() }}</div>
</div>
@endsection
