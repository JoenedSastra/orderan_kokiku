@extends('layouts.app')
@section('title', 'Stock Harian')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h2 class="h5 mb-0">Stock Harian</h2>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Rekap otomatis Barang Masuk &amp; Keluar untuk Sayur, Daging, Saos.</p>
    </div>
    <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
        <span class="text-muted">s/d</span>
        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
        <button class="btn btn-sm text-white" style="background:var(--kk-accent)">Filter</button>
    </form>
</div>

<ul class="nav nav-tabs mb-3">
    @foreach($categories as $i => $cat)
    <li class="nav-item">
        <a class="nav-link {{ $i === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#cat{{ $cat['category']->id }}">
            {{ $cat['category']->name }}
        </a>
    </li>
    @endforeach
</ul>

<div class="tab-content">
    @forelse($categories as $i => $cat)
    <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="cat{{ $cat['category']->id }}">
        @if(empty($cat['items']))
            <p class="text-muted">Belum ada barang di kategori {{ $cat['category']->name }}.</p>
        @endif

        @foreach($cat['items'] as $itemReport)
        <div class="kk-stat-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold">
                    {{ $itemReport['item']->name }}
                    <span class="text-muted" style="font-size:0.8rem;">({{ $itemReport['item']->unit }})</span>
                </div>
                <span class="text-muted" style="font-size:0.78rem;">Min. Stok: {{ $itemReport['item']->min_stock }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Tanggal</th><th>Masuk</th><th>Keluar</th><th>Sisa</th></tr>
                    </thead>
                    <tbody>
                        @foreach($itemReport['rows'] as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d M Y') }}</td>
                            <td class="text-success">{{ $row['masuk'] > 0 ? '+' . $row['masuk'] : '-' }}</td>
                            <td class="text-danger">{{ $row['keluar'] > 0 ? '-' . $row['keluar'] : '-' }}</td>
                            <td class="fw-semibold {{ $row['sisa'] <= $itemReport['item']->min_stock ? 'text-danger' : '' }}">
                                {{ $row['sisa'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
    @empty
    <p class="text-muted">Kategori Sayur, Daging, atau Saos belum tersedia.</p>
    @endforelse
</div>
@endsection
