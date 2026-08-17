@extends('layouts.app')
@section('title', 'Laporan')
@section('content')

<div class="kk-stat-card mb-3">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end" autocomplete="off">
        <div class="col-12 mb-2">
            <label class="form-label d-block text-muted" style="font-size:0.85rem;">Jenis Laporan</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="type" id="type_stock_gudang_utama" value="stock_gudang_utama" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_gudang_utama' ? 'checked' : '' }}>
                <label class="btn btn-sm btn-outline-primary shadow-none text-nowrap" for="type_stock_gudang_utama">
                    <i class="bi bi-box-seam"></i> Stock Gudang Utama
                </label>
                
                <input type="radio" class="btn-check" name="type" id="type_stock_gudang_resto" value="stock_gudang_resto" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_gudang_resto' ? 'checked' : '' }}>
                <label class="btn btn-sm btn-outline-primary shadow-none text-nowrap" for="type_stock_gudang_resto">
                    <i class="bi bi-shop"></i> Stock Gudang Resto
                </label>
                
                <input type="radio" class="btn-check" name="type" id="type_stock_kasir" value="stock_kasir" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_kasir' ? 'checked' : '' }}>
                <label class="btn btn-sm btn-outline-primary shadow-none text-nowrap" for="type_stock_kasir">
                    <i class="bi bi-calculator"></i> Stock Kasir
                </label>
                
                <input type="radio" class="btn-check" name="type" id="type_stock_kitchen" value="stock_kitchen" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_kitchen' ? 'checked' : '' }}>
                <label class="btn btn-sm btn-outline-primary shadow-none text-nowrap" for="type_stock_kitchen">
                    <i class="bi bi-egg-fried"></i> Stock Kitchen
                </label>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label text-muted" style="font-size:0.85rem;">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" onchange="this.form.submit()">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label text-muted" style="font-size:0.85rem;">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" onchange="this.form.submit()">
        </div>
    </form>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="fw-semibold">
        {{ $label }}
        <span class="text-muted fw-normal" style="font-size:0.85rem;">
            ({{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }})
        </span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.pdf', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
        <a href="{{ route('admin.reports.excel', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
    </div>
</div>

@php
    $isBarangKeluarDivisi = in_array($type, ['barang_keluar_kitchen', 'barang_keluar_kasir']);
    $isStockReport = str_starts_with($type, 'stock_');
    $tableClasses = 'table-hover';
    $theadClasses = 'table-light';
    
    if ($type === 'barang_masuk') {
        $tableClasses = 'table-success table-hover';
        $theadClasses = 'table-success';
    } elseif ($type === 'barang_keluar_kasir') {
        $tableClasses = 'table-info table-bordered table-striped';
        $theadClasses = 'table-info';
    } elseif ($type === 'barang_keluar_kitchen') {
        $tableClasses = 'table-danger table-bordered table-striped';
        $theadClasses = 'table-danger';
    }
@endphp
<div class="{{ $isBarangKeluarDivisi ? '' : 'kk-stat-card' }}">
    <div class="table-responsive">
        <table class="table table-sm text-center align-middle {{ $tableClasses }} mb-0">
            <thead class="{{ $theadClasses }}">
                <tr>
                    <th class="text-center align-middle" style="{{ $isStockReport ? 'width: 4%;' : 'width: 50px;' }}">No</th>
                    @foreach($headings as $h)
                        @php
                            $width = '';
                            if ($isStockReport) {
                                $width = match($h) {
                                    'Hari, Jam & Tanggal' => 'width: 16%;',
                                    'Nama Barang' => 'width: 14%;',
                                    'Masuk' => 'width: 6%;',
                                    'Keluar' => 'width: 6%;',
                                    'Stock' => 'width: 6%;',
                                    'Satuan' => 'width: 8%;',
                                    'Devisi' => 'width: 12%;',
                                    'Keterangan' => 'width: 18%;',
                                    'Dicatat Oleh' => 'width: 10%;',
                                    default => ''
                                };
                            }
                        @endphp
                    <th class="text-center align-middle" style="{{ $width }} {!! !$isStockReport ? 'white-space: nowrap;' : '' !!}">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                <tr>
                    <td class="text-center align-middle fw-bold">{{ $index + 1 }}</td>
                    @foreach($row as $key => $value)
                    <td class="text-center align-middle {{ $key === 'masuk' ? 'text-success fw-semibold' : ($key === 'keluar' ? 'text-danger fw-semibold' : ($key === 'sisa' ? 'text-primary fw-bold' : ($key === 'barang' ? 'fw-bold' : ''))) }} {{ in_array($key, ['barang', 'keterangan']) ? 'text-wrap' : 'text-nowrap' }}">
                        @if($key === 'master')
                            <span class="badge" style="background:#bfdbfe;color:#1d4ed8;font-weight:600;">{{ $value }}</span>
                        @elseif($key === 'dicatat_oleh' && ($isBarangKeluarDivisi || $isStockReport))
                            <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $value }}</span>
                        @else
                            {{ $value }}
                        @endif
                    </td>
                    @endforeach
                </tr>
                @empty
                <tr><td colspan="{{ count($headings) + 1 }}" class="text-center text-muted py-3">Tidak ada data pada rentang tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
