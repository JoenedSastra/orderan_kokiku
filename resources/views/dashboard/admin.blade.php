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
        <a href="{{ route('admin.stock_in.index') }}" class="kk-quick-action">
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
    <div class="kk-section-header" style="flex-wrap:wrap; gap:0.5rem;">
        <div class="kk-section-title">
            <i class="bi bi-graph-up-arrow"></i> Grafik Aktivitas
        </div>
        {{-- Tab switcher --}}
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="kk-chart-tabs" id="chartTabs">
                <button class="kk-chart-tab active" data-tab="harian" id="tabHarian">
                    <i class="bi bi-calendar-day me-1"></i>Hari Ini
                </button>
                <button class="kk-chart-tab" data-tab="bulanan" id="tabBulanan">
                    <i class="bi bi-calendar-month me-1"></i>Bulan
                </button>
                <button class="kk-chart-tab" data-tab="tahunan" id="tabTahunan">
                    <i class="bi bi-calendar2-range me-1"></i>Tahun
                </button>
            </div>
            {{-- Sub-control per tab --}}
            <span id="ctrlHarian" class="kk-chart-ctrl">
                <span class="kk-chart-date-badge">
                    <i class="bi bi-calendar-check me-1"></i>
                    <span id="labelHariIni">{{ now()->translatedFormat('l, d F Y') }}</span>
                </span>
            </span>
            <span id="ctrlBulanan" class="kk-chart-ctrl" style="display:none;">
                <select id="chartMonth" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                    @php $bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                    @foreach ($bulanIndo as $bIdx => $bNama)
                        <option value="{{ $bIdx + 1 }}" {{ ($bIdx + 1) === now()->month ? 'selected' : '' }}>{{ $bNama }}</option>
                    @endforeach
                </select>
                <select id="chartMonthYear" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                    @for ($y = now()->year; $y >= 2022; $y--)
                        <option value="{{ $y }}" {{ $y === now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </span>
            <span id="ctrlTahunan" class="kk-chart-ctrl" style="display:none;">
                <span class="kk-chart-date-badge">
                    <i class="bi bi-calendar2-week me-1"></i>
                    <span id="labelRentangTahun">{{ $tahunMulaiGrafik }} – {{ now()->year }}</span>
                </span>
            </span>
        </div>
    </div>

    {{-- Subtitle tanggal aktif --}}
    <div id="chartSubtitle" style="font-size:0.8rem; color:var(--kk-muted,#888); margin-bottom:1rem; margin-top:-0.25rem; padding-left:0.1rem;">
        Menampilkan data hari ini
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div style="background:var(--kk-surface-2); border-radius:var(--kk-radius-sm); padding:1rem;">
                <div style="font-size:0.78rem; font-weight:700; color:var(--kk-success); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.75rem;">
                    <i class="bi bi-box-arrow-in-down-right me-1"></i>Barang Masuk
                </div>
                <canvas id="chartMasuk" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div style="background:var(--kk-surface-2); border-radius:var(--kk-radius-sm); padding:1rem;">
                <div style="font-size:0.78rem; font-weight:700; color:var(--kk-danger); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.75rem;">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Barang Keluar
                </div>
                <canvas id="chartKeluar" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div style="background:var(--kk-surface-2); border-radius:var(--kk-radius-sm); padding:1rem;">
                <div style="font-size:0.78rem; font-weight:700; color:var(--kk-orange); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.75rem;">
                    <i class="bi bi-clipboard2-check-fill me-1"></i>Permintaan
                </div>
                <canvas id="chartPermintaan" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlHarian   = '{{ route('admin.dashboard.chart-data-harian') }}';
    const urlBulanan  = '{{ route('admin.dashboard.chart-data') }}';
    const urlTahunan  = '{{ route('admin.dashboard.chart-data-tahunan') }}';

    let activeTab = 'harian';
    let charts    = { masuk: null, keluar: null, permintaan: null };

    /* ── Helpers ─────────────────────────────────────────────── */
    function destroyCharts() {
        ['masuk', 'keluar', 'permintaan'].forEach(k => {
            if (charts[k]) { charts[k].destroy(); charts[k] = null; }
        });
    }

    function buildChart(canvasId, label, data, labels, color, type = 'bar') {
        const ctx = document.getElementById(canvasId);
        return new window.Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: color + '99',
                    borderColor: color,
                    borderWidth: 2,
                    borderRadius: type === 'bar' ? 6 : 0,
                    maxBarThickness: 32,
                    fill: type === 'line',
                    tension: 0.4,
                    pointRadius: type === 'line' ? 3 : 0,
                    pointHoverRadius: type === 'line' ? 6 : 0,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } }
                },
            },
        });
    }

    function renderCharts(json, type = 'bar') {
        destroyCharts();
        charts.masuk      = buildChart('chartMasuk',      'Barang Masuk',  json.masuk,      json.labels, '#10b981', type);
        charts.keluar     = buildChart('chartKeluar',     'Barang Keluar', json.keluar,     json.labels, '#ef4444', type);
        charts.permintaan = buildChart('chartPermintaan', 'Permintaan',    json.permintaan, json.labels, '#ff6b35', type);
    }

    /* ── Loader per mode ─────────────────────────────────────── */
    function loadHarian() {
        const today = new Date();
        const tgl   = today.toISOString().slice(0, 10);
        const opts  = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
        document.getElementById('labelHariIni').textContent = today.toLocaleDateString('id-ID', opts);
        document.getElementById('chartSubtitle').textContent = 'Aktivitas per jam — ' + today.toLocaleDateString('id-ID', opts);

        fetch(urlHarian + '?date=' + tgl)
            .then(r => r.json())
            .then(json => renderCharts(json, 'line'))
            .catch(() => console.error('Gagal muat data harian.'));
    }

    function loadBulanan() {
        const month = document.getElementById('chartMonth').value;
        const year  = document.getElementById('chartMonthYear').value;
        const namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('chartSubtitle').textContent =
            'Aktivitas per minggu — ' + namaBulan[parseInt(month) - 1] + ' ' + year;

        // Gunakan endpoint bulanan dengan filter tahun, lalu ambil 1 bulan saja
        fetch(urlBulanan + '?year=' + year + '&month=' + month)
            .then(r => r.json())
            .then(json => {
                // Filter hanya bulan yang dipilih (ambil index bulan - 1)
                const idx = parseInt(month) - 1;
                // Buat data per minggu dalam 1 bulan (gunakan dummy 4 minggu dari total bulk)
                // Atau tampilkan semua bulan dengan highlight bulan terpilih
                renderCharts(json, 'bar');
            })
            .catch(() => console.error('Gagal muat data bulanan.'));
    }

    function loadTahunan() {
        document.getElementById('chartSubtitle').textContent =
            'Aktivitas per tahun — ' + document.getElementById('labelRentangTahun').textContent;

        fetch(urlTahunan)
            .then(r => r.json())
            .then(json => renderCharts(json, 'bar'))
            .catch(() => console.error('Gagal muat data tahunan.'));
    }

    /* ── Tab switching ───────────────────────────────────────── */
    const tabs = document.querySelectorAll('.kk-chart-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            activeTab = this.dataset.tab;

            // Tampilkan kontrol yang sesuai
            document.getElementById('ctrlHarian').style.display  = activeTab === 'harian'  ? '' : 'none';
            document.getElementById('ctrlBulanan').style.display  = activeTab === 'bulanan'  ? '' : 'none';
            document.getElementById('ctrlTahunan').style.display  = activeTab === 'tahunan'  ? '' : 'none';

            if (activeTab === 'harian')  loadHarian();
            if (activeTab === 'bulanan') loadBulanan();
            if (activeTab === 'tahunan') loadTahunan();
        });
    });

    // Kontrol bulan
    document.getElementById('chartMonth').addEventListener('change', loadBulanan);
    document.getElementById('chartMonthYear').addEventListener('change', loadBulanan);

    // Load default: harian
    loadHarian();
});
</script>
@endpush

@endsection

