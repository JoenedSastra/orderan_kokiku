@extends('layouts.app')
@section('title', 'Sisa Stok')
@section('content')
<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2 class="h5 mb-0">Laporan Sisa Stok</h2>
    <div class="d-flex align-items-center gap-2 flex-nowrap">
        <div class="kk-search-box">
            <i class="bi bi-search"></i>
            <form action="{{ route('kitchen.stock_sisa.index') }}" method="GET" class="m-0">
                <input type="text" name="kk_search" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang..." value="{{ request('kk_search') }}" autocomplete="off">
            </form>
        </div>
    </div>
</div>
<div class="kk-stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Barang Masuk</th>
                    <th class="text-center">Barang Keluar</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $s)
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $s->name }}</td>
                    <td class="text-center">{{ $s->total_masuk }}</td>
                    <td class="text-center">{{ $s->total_keluar }}</td>
                    <td class="text-center">{{ $s->sisa_stok }}</td>
                    <td class="text-center">{{ $s->unit }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data stok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="p-3">{{ $items->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
