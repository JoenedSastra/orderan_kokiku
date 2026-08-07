@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')

{{-- Hero Greeting Card --}}
<div class="kk-hero-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="kk-hero-greeting">🔥 Panel Administrator</div>
            <div class="kk-hero-name">Halo, {{ $user->name }}!</div>
            <div class="kk-hero-sub">Selamat datang di pusat kendali inventaris Kokiku.</div>
            <div class="kk-hero-badge">
                <i class="bi bi-calendar3"></i>
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
        <div class="col-md-4 d-none d-md-flex justify-content-end align-items-center">
            <div style="font-size:5rem; opacity:0.12; line-height:1; position:relative; z-index:1;">🔥</div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('admin.stock_in.create') }}" class="kk-quick-action">
            <i class="bi bi-box-arrow-in-down-right" style="color:var(--kk-success);"></i>
            <span>Barang Masuk</span>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('admin.orders.index') }}" class="kk-quick-action">
            <i class="bi bi-clipboard2-check-fill" style="color:var(--kk-orange);"></i>
            <span>Permintaan</span>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('admin.stock.index') }}" class="kk-quick-action">
            <i class="bi bi-layers-fill" style="color:var(--kk-info);"></i>
            <span>Stok Barang</span>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('admin.items.index') }}" class="kk-quick-action">
            <i class="bi bi-box-seam-fill" style="color:var(--kk-warning);"></i>
            <span>Master Barang</span>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('admin.reports.index') }}" class="kk-quick-action">
            <i class="bi bi-bar-chart-fill" style="color:var(--kk-danger);"></i>
            <span>Laporan</span>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <a href="{{ route('admin.users.index') }}" class="kk-quick-action">
            <i class="bi bi-people-fill" style="color:#8b5cf6;"></i>
            <span>Pengguna</span>
        </a>
    </div>
</div>

{{-- Stat Cards Row 1 --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card gradient-orange">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            </div>
            <div class="kk-stat-value">{{ $totalBarang }}</div>
            <div class="kk-stat-label">Total Jenis Barang</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card gradient-green">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-box-arrow-in-down-right"></i></div>
            </div>
            <div class="kk-stat-value">{{ $masukHariIni }}</div>
            <div class="kk-stat-label">Barang Masuk Hari Ini</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card gradient-red">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-box-arrow-up-right"></i></div>
            </div>
            <div class="kk-stat-value">{{ $keluarHariIni }}</div>
            <div class="kk-stat-label">Barang Keluar Hari Ini</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card gradient-amber">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="kk-stat-icon"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <div class="kk-stat-value">{{ $permintaanMenunggu }}</div>
            <div class="kk-stat-label">Permintaan Menunggu</div>
        </div>
    </div>
</div>

{{-- Stat Cards Row 2 --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="kk-stat-value" style="color:var(--kk-danger);">{{ $stokRendah }}</div>
                    <div class="kk-stat-label">Stok Rendah / Habis</div>
                </div>
                <div class="kk-stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="kk-stat-value" style="color:var(--kk-success);">{{ $permintaanDisetujui }}</div>
                    <div class="kk-stat-label">Permintaan Disetujui</div>
                </div>
                <div class="kk-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="kk-stat-value" style="color:var(--kk-danger);">{{ $permintaanDitolak }}</div>
                    <div class="kk-stat-label">Permintaan Ditolak</div>
                </div>
                <div class="kk-stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="kk-stat-value">{{ $totalSupplier }}</div>
                    <div class="kk-stat-label">Total Supplier</div>
                </div>
                <div class="kk-stat-icon orange"><i class="bi bi-truck-front-fill"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders Table --}}
<div class="kk-stat-card mb-4">
    <div class="kk-section-header">
        <div class="kk-section-title">
            <i class="bi bi-clock-history"></i>
            Permintaan Barang Terbaru
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    @if($ordersRecent->isEmpty())
        <div class="text-center py-4">
            <div style="font-size:2.5rem; color:var(--kk-border);"><i class="bi bi-inbox"></i></div>
            <div class="text-muted mt-2" style="font-size:0.875rem;">Belum ada permintaan barang.</div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Dari</th><th>Barang</th><th>Jumlah</th><th>Status</th><th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordersRecent as $order)
                    <tr>
                        <td class="text-muted" style="font-size:0.8rem;">#{{ $order->id }}</td>
                        <td class="fw-semibold" style="font-size:0.875rem;">{{ $order->user->name }}</td>
                        <td style="font-size:0.875rem;">{{ $order->item->name }}</td>
                        <td style="font-size:0.875rem;">{{ $order->quantity }} {{ $order->item->unit }}</td>
                        <td><span class="kk-badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span></td>
                        <td class="text-muted" style="font-size:0.8rem;">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Charts --}}
<div class="kk-stat-card mb-3">
    <div class="kk-section-header">
        <div class="kk-section-title">
            <i class="bi bi-graph-up-arrow"></i> Grafik Aktivitas Bulanan
        </div>
        <select id="chartYear" class="form-select form-select-sm" style="width:auto;">
            @for ($y = now()->year; $y >= 2026; $y--)
                <option value="{{ $y }}" {{ $y === now()->year ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="row g-3">
        <div class="col-lg-4">
            <div style="background:var(--kk-surface-2); border-radius:var(--kk-radius-sm); padding:1rem;">
                <div style="font-size:0.78rem; font-weight:700; color:var(--kk-success); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.75rem;">
                    <i class="bi bi-box-arrow-in-down-right me-1"></i>Barang Masuk
                </div>
                <canvas id="chartMasukBulanan" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div style="background:var(--kk-surface-2); border-radius:var(--kk-radius-sm); padding:1rem;">
                <div style="font-size:0.78rem; font-weight:700; color:var(--kk-danger); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.75rem;">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Barang Keluar
                </div>
                <canvas id="chartKeluarBulanan" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div style="background:var(--kk-surface-2); border-radius:var(--kk-radius-sm); padding:1rem;">
                <div style="font-size:0.78rem; font-weight:700; color:var(--kk-orange); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.75rem;">
                    <i class="bi bi-clipboard2-check-fill me-1"></i>Permintaan
                </div>
                <canvas id="chartPermintaanBulanan" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const endpointUrl = '{{ route('admin.dashboard.chart-data') }}';
        const yearSelect  = document.getElementById('chartYear');
        let chartMasuk, chartKeluar, chartPermintaan;

        function buildChart(canvasId, label, data, labels, color) {
            const ctx = document.getElementById(canvasId);
            return new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: color,
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    },
                },
            });
        }

        function loadChartData(year) {
            fetch(endpointUrl + '?year=' + year)
                .then(res => res.json())
                .then(json => {
                    if (chartMasuk) chartMasuk.destroy();
                    if (chartKeluar) chartKeluar.destroy();
                    if (chartPermintaan) chartPermintaan.destroy();
                    chartMasuk      = buildChart('chartMasukBulanan',      'Barang Masuk',  json.masuk,       json.labels, '#10b981');
                    chartKeluar     = buildChart('chartKeluarBulanan',     'Barang Keluar', json.keluar,      json.labels, '#ef4444');
                    chartPermintaan = buildChart('chartPermintaanBulanan', 'Permintaan',    json.permintaan,  json.labels, '#ff6b35');
                })
                .catch(() => console.error('Gagal memuat data grafik.'));
        }

        loadChartData(yearSelect.value);
        yearSelect.addEventListener('change', function () { loadChartData(this.value); });
    });
</script>
@endpush

@endsection
