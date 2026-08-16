@extends('layouts.app')
@section('title', 'Laporan')
@section('content')

<div class="kk-stat-card mb-3">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end" autocomplete="off">
        <div class="col-md-4">
            <label class="form-label">Jenis Laporan</label>
            <select name="type" class="form-select" onchange="this.form.submit()" autocomplete="off">
                <option value="barang_masuk" {{ $type === 'barang_masuk' ? 'selected' : '' }}>Laporan Barang Masuk Harian</option>
                <option value="barang_keluar_kasir" {{ $type === 'barang_keluar_kasir' ? 'selected' : '' }}>Laporan Barang Keluar Kasir</option>
                <option value="barang_keluar_kitchen" {{ $type === 'barang_keluar_kitchen' ? 'selected' : '' }}>Laporan Barang Keluar Kitchen</option>
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" onchange="this.form.submit()">
        </div>
        <div class="col-6 col-md-4">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="this.form.submit()">
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
@endphp
<div class="{{ $isBarangKeluarDivisi ? '' : 'kk-stat-card' }}">
    <div class="table-responsive">
        <table class="table table-sm text-center align-middle {{ $isBarangKeluarDivisi ? 'table-bordered table-striped' : 'table-hover' }} mb-0">
            <thead class="{{ $isBarangKeluarDivisi ? 'table-warning' : 'table-light' }}">
                <tr>
                    <th class="text-center align-middle" style="width: 50px;">No</th>
                    @foreach($headings as $h)
                    <th class="text-center align-middle text-nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                <tr>
                    <td class="text-center align-middle fw-bold">{{ $index + 1 }}</td>
                    @foreach($row as $key => $value)
                    <td class="text-center align-middle text-nowrap">
                        @if($isBarangKeluarDivisi && $key === 'dicatat_oleh')
                            @php
                                // Extra formatting to match the badge look in the Kasir/Kitchen view
                                $badgeColor = $type === 'barang_keluar_kitchen' ? '#bfdbfe' : '#bfdbfe'; // Assuming Kasir uses similar or same badge
                                $textColor = $type === 'barang_keluar_kitchen' ? '#1d4ed8' : '#1d4ed8';
                            @endphp
                            <span class="badge" style="background:{{ $badgeColor }};color:{{ $textColor }};font-weight:600;">{{ $value }}</span>
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
