@extends('layouts.app')
@section('title', 'Total Stock Barang')
@section('content')
<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2 class="h5 mb-0">Total Stock Barang</h2>
    <div class="d-flex align-items-center gap-2 flex-nowrap">
        <div class="kk-search-box">
            <i class="bi bi-search"></i>
            <form action="{{ route('kasir.stock_harian.index') }}" method="GET" class="m-0">
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
                    <th class="text-center">Hari, Jam, &amp; Tanggal</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Master</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                @php $aktivitas = $item->latestMasukActivity(); @endphp
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center">{{ $aktivitas?->created_at?->translatedFormat('l, d M Y H:i') ?? '-' }}</td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $item->name }}</td>
                    <td class="text-center">{{ $item->stokByLocation($item->master_location) }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-center"><span class="badge" style="background:#bfdbfe;color:#1d4ed8;font-weight:600;">{{ $item->masterLocationLabel() }}</span></td>
                    <td class="text-center">{{ $aktivitas?->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $aktivitas?->user?->role?->name ?? '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="p-3">{{ $items->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
