@extends('layouts.app')
@section('title', 'Laporan')
@section('content')

<div class="kk-stat-card mb-3">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end" autocomplete="off">
        <div class="col-12 mb-2">
            <label class="form-label d-block text-muted mb-2" style="font-size:0.8rem; letter-spacing:0.06em; text-transform:uppercase; font-weight:600;">Jenis Laporan</label>
            <div class="d-flex flex-wrap gap-2">

                {{-- Gudang Utama --}}
                <input type="radio" class="btn-check" name="type" id="type_stock_gudang_utama" value="stock_gudang_utama" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_gudang_utama' ? 'checked' : '' }}>
                <label class="kk-report-tab" for="type_stock_gudang_utama" style="--rt-accent:#3b82f6; --rt-glow:rgba(59,130,246,0.4);">
                    <div class="kk-report-tab-icon" style="background:linear-gradient(135deg,rgba(59,130,246,0.35),rgba(59,130,246,0.15)); color:#60a5fa; border-color:rgba(59,130,246,0.35);">
                        <i class="bi bi-building"></i>
                    </div>
                    <span class="kk-report-tab-text">
                        <span class="kk-report-tab-name">Gudang Utama</span>
                        <span class="kk-report-tab-sub">Stok barang utama</span>
                    </span>
                </label>

                {{-- Gudang Resto --}}
                <input type="radio" class="btn-check" name="type" id="type_stock_gudang_resto" value="stock_gudang_resto" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_gudang_resto' ? 'checked' : '' }}>
                <label class="kk-report-tab" for="type_stock_gudang_resto" style="--rt-accent:#22c55e; --rt-glow:rgba(34,197,94,0.4);">
                    <div class="kk-report-tab-icon" style="background:linear-gradient(135deg,rgba(34,197,94,0.35),rgba(34,197,94,0.15)); color:#4ade80; border-color:rgba(34,197,94,0.35);">
                        <i class="bi bi-shop"></i>
                    </div>
                    <span class="kk-report-tab-text">
                        <span class="kk-report-tab-name">Gudang Resto</span>
                        <span class="kk-report-tab-sub">Stok restoran</span>
                    </span>
                </label>

                {{-- Kasir --}}
                <input type="radio" class="btn-check" name="type" id="type_stock_kasir" value="stock_kasir" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_kasir' ? 'checked' : '' }}>
                <label class="kk-report-tab" for="type_stock_kasir" style="--rt-accent:#ef4444; --rt-glow:rgba(239,68,68,0.4);">
                    <div class="kk-report-tab-icon" style="background:linear-gradient(135deg,rgba(239,68,68,0.35),rgba(239,68,68,0.15)); color:#f87171; border-color:rgba(239,68,68,0.35);">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <span class="kk-report-tab-text">
                        <span class="kk-report-tab-name">Kasir</span>
                        <span class="kk-report-tab-sub">Stok kasir</span>
                    </span>
                </label>

                {{-- Kitchen --}}
                <input type="radio" class="btn-check" name="type" id="type_stock_kitchen" value="stock_kitchen" autocomplete="off" onchange="this.form.submit()" {{ $type === 'stock_kitchen' ? 'checked' : '' }}>
                <label class="kk-report-tab" for="type_stock_kitchen" style="--rt-accent:#eab308; --rt-glow:rgba(234,179,8,0.4);">
                    <div class="kk-report-tab-icon" style="background:linear-gradient(135deg,rgba(234,179,8,0.35),rgba(234,179,8,0.15)); color:#facc15; border-color:rgba(234,179,8,0.35);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4c-4.42 0-8 3.58-8 8v1a1 1 0 0 0 1 1h16.29l3.42 3.41 1.41-1.41L21 14.29V13c0-4.42-3.58-8-8-9m0 2c3.31 0 6 2.69 6 6v1H6v-1c0-3.31 2.69-6 6-6z"/>
                        </svg>
                    </div>
                    <span class="kk-report-tab-text">
                        <span class="kk-report-tab-name">Kitchen</span>
                        <span class="kk-report-tab-sub">Stok dapur</span>
                    </span>
                </label>
            </div>
        </div>

        {{-- Date Range --}}
        <div class="col-12 col-md-auto mt-1">
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center flex-wrap justify-content-center rounded-3 gap-1 px-3 py-1" style="background:var(--kk-surface-2); border:1px solid var(--kk-border); font-size:0.78rem;">
                    <i class="bi bi-calendar3 text-muted me-1" style="font-size:0.8rem;"></i>
                    <span class="text-muted" style="font-size:0.75rem;">Dari</span>
                    <input type="date" name="start_date" class="border-0 bg-transparent p-0" style="width:96px; font-size:0.75rem; outline:none; color:var(--kk-text);" value="{{ $startDate }}" onchange="this.form.submit()">
                    <span class="text-muted mx-1">—</span>
                    <span class="text-muted" style="font-size:0.75rem;">Sampai</span>
                    <input type="date" name="end_date" class="border-0 bg-transparent p-0" style="width:96px; font-size:0.75rem; outline:none; color:var(--kk-text);" value="{{ $endDate }}" onchange="this.form.submit()">
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* ===== Premium Report Tabs ===== */
.kk-report-tab {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1 1 130px;
    gap: 9px;
    padding: 5px 10px 5px 6px;
    border-radius: 12px;
    cursor: pointer;
    border: 1px solid var(--kk-border, rgba(0,0,0,0.1));
    background: var(--kk-surface-2, rgba(0,0,0,0.04));
    transition: all 0.22s ease;
    text-decoration: none;
    min-width: 0;
    user-select: none;
}
.kk-report-tab:hover {
    background: var(--kk-surface-3, rgba(0,0,0,0.07));
    border-color: var(--rt-accent, #3b82f6);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px var(--rt-glow, rgba(59,130,246,0.3));
}
.btn-check:checked + .kk-report-tab {
    background: var(--kk-surface-3, rgba(0,0,0,0.1));
    border-color: var(--rt-accent, #3b82f6);
    box-shadow: 0 4px 18px var(--rt-glow, rgba(59,130,246,0.35));
    transform: translateY(-2px);
}
.kk-report-tab-icon {
    width: 28px; height: 28px;
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
    border: 1px solid;
    transition: transform 0.22s, box-shadow 0.22s;
}
.kk-report-tab:hover .kk-report-tab-icon,
.btn-check:checked + .kk-report-tab .kk-report-tab-icon {
    transform: scale(1.15) rotate(-5deg);
    box-shadow: 0 2px 10px var(--rt-glow, rgba(59,130,246,0.3));
}
.kk-report-tab-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}
.kk-report-tab-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--kk-text, #1e293b);
    white-space: nowrap;
}
.kk-report-tab-sub {
    font-size: 0.62rem;
    color: var(--kk-text-muted, #64748b);
    white-space: nowrap;
}
.btn-check:checked + .kk-report-tab .kk-report-tab-name {
    color: var(--rt-accent, #3b82f6);
}
.btn-check:checked + .kk-report-tab .kk-report-tab-sub {
    color: var(--kk-text-muted, #64748b);
}

/* Dark mode overrides */
[data-theme="dark"] .kk-report-tab {
    border-color: rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.04);
}
[data-theme="dark"] .kk-report-tab:hover {
    background: rgba(255,255,255,0.08);
}
[data-theme="dark"] .btn-check:checked + .kk-report-tab {
    background: rgba(255,255,255,0.10);
}
[data-theme="dark"] .kk-report-tab-name {
    color: rgba(255,255,255,0.85);
}
[data-theme="dark"] .kk-report-tab-sub {
    color: rgba(255,255,255,0.45);
}
[data-theme="dark"] .btn-check:checked + .kk-report-tab .kk-report-tab-name {
    color: #fff;
}
[data-theme="dark"] .btn-check:checked + .kk-report-tab .kk-report-tab-sub {
    color: rgba(255,255,255,0.65);
}
</style>

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
<style>
    #tabelLaporan { border: none !important; }
    #tabelLaporan thead th { border-top: none !important; }
    #tabelLaporan th:first-child, #tabelLaporan td:first-child { border-left: none !important; }
    #tabelLaporan th:last-child, #tabelLaporan td:last-child { border-right: none !important; }
    #tabelLaporan tbody tr:last-child td { border-bottom: none !important; }
</style>
<div class="{{ $isBarangKeluarDivisi ? '' : 'kk-stat-card' }} mb-4" style="border: 2px solid #dc2626; padding: 0; overflow: hidden; border-radius: 8px;">
    <div class="table-responsive m-0" style="background-color: var(--kk-surface);">
        <table class="table table-sm text-center align-middle {{ $tableClasses }} mb-0" id="tabelLaporan">
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
