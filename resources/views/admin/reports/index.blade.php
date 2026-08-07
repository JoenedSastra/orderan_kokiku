@extends('layouts.app')
@section('title', 'Laporan')
@section('content')

<div class="kk-stat-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Jenis Laporan</label>
            <select name="type" class="form-select">
                <option value="barang_masuk" {{ $type === 'barang_masuk' ? 'selected' : '' }}>Laporan Barang Masuk</option>
                <option value="barang_keluar" {{ $type === 'barang_keluar' ? 'selected' : '' }}>Laporan Barang Keluar</option>
                <option value="permintaan" {{ $type === 'permintaan' ? 'selected' : '' }}>Laporan Permintaan</option>
                <option value="stock_kitchen" {{ $type === 'stock_kitchen' ? 'selected' : '' }}>Laporan Stock Kitchen</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-2">
            <button class="btn text-white w-100" style="background:var(--kk-accent)">Tampilkan</button>
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

<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light">
                <tr>
                    @foreach($headings as $h)
                    <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    @foreach($row as $value)
                    <td>{{ $value }}</td>
                    @endforeach
                </tr>
                @empty
                <tr><td colspan="{{ count($headings) }}" class="text-center text-muted py-3">Tidak ada data pada rentang tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
