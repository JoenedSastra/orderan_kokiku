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
            {{-- Tanggal Saja (Bisa Diklik untuk memilih tanggal / hari sebelumnya) --}}
            <div id="ctrlHarian" class="kk-chart-ctrl position-relative" style="display:inline-flex; align-items:center;">
                <div id="badgeTanggal" class="kk-chart-date-badge d-flex align-items-center" style="background:var(--kk-surface-2); border:1px solid var(--kk-border); border-radius:8px; padding:0.4rem 0.75rem; font-size:0.875rem; font-weight:500; cursor:pointer; transition:all 0.2s;" title="Klik untuk pilih tanggal">
                    <i class="bi bi-calendar-check me-1 text-primary"></i>
                    <span id="labelHariIni">{{ now()->translatedFormat('d F Y') }}</span>
                    <i class="bi bi-chevron-down ms-2 text-muted" style="font-size:0.7rem;"></i>
                </div>
                <input type="date" id="inputTanggalChart" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2;" title="Pilih tanggal">
            </div>

            {{-- Label Rentang Bulan (tersembunyi secara default) --}}
            <div id="ctrlBulanan" class="kk-chart-ctrl position-relative" style="display:none; align-items:center;">
                <div id="badgeBulan" class="kk-chart-date-badge d-flex align-items-center" style="background:var(--kk-surface-2); border:1px solid var(--kk-border); border-radius:8px; padding:0.4rem 0.75rem; font-size:0.875rem; font-weight:500; cursor:pointer; transition:all 0.2s;" title="Klik untuk pilih bulan">
                    <i class="bi bi-calendar-month me-1 text-primary"></i>
                    <span id="labelBulanIni">{{ now()->translatedFormat('F Y') }}</span>
                    <i class="bi bi-chevron-down ms-2 text-muted" style="font-size:0.7rem;"></i>
                </div>
                <input type="month" id="inputBulanChart" value="{{ now()->format('Y-m') }}" max="{{ now()->format('Y-m') }}" style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2;" title="Pilih bulan">
            </div>

            {{-- Tombol Mode --}}
            <button id="btnLihatSemua" class="btn btn-sm" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius:8px; padding:0.4rem 0.75rem; font-size:0.875rem; font-weight:500; transition: all 0.2s;">
                <i class="bi bi-calendar-day me-1"></i> Hari Ini
            </button>
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

    let isShowingAll = false;
    let currentDate  = '{{ now()->toDateString() }}';
    let currentMonth = '{{ now()->format('Y-m') }}';
    let charts       = { masuk: null, keluar: null, permintaan: null };

    const inputTanggal  = document.getElementById('inputTanggalChart');
    const labelHariIni  = document.getElementById('labelHariIni');
    
    const inputBulan    = document.getElementById('inputBulanChart');
    const labelBulanIni = document.getElementById('labelBulanIni');

    const chartSubtitle = document.getElementById('chartSubtitle');
    const btnLihatSemua = document.getElementById('btnLihatSemua');
    const ctrlHarian    = document.getElementById('ctrlHarian');
    const ctrlBulanan   = document.getElementById('ctrlBulanan');

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

    function formatTanggalIndo(dateStr) {
        const parts = dateStr.split('-');
        if (parts.length !== 3) return { tanggalSaja: dateStr, lengkap: dateStr };
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        const d = new Date(year, month, day);
        
        const namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const namaHari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        
        return {
            tanggalSaja: `${day} ${namaBulan[month]} ${year}`,
            lengkap: `${namaHari[d.getDay()]}, ${day} ${namaBulan[month]} ${year}`
        };
    }

    function formatBulanIndo(monthStr) {
        const parts = monthStr.split('-');
        if (parts.length !== 2) return monthStr;
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return `${namaBulan[month]} ${year}`;
    }

    /* ── Loader per mode ─────────────────────────────────────── */
    function loadHarian(dateStr) {
        const tgl = dateStr || currentDate;
        currentDate = tgl;
        inputTanggal.value = tgl;

        const infoTgl = formatTanggalIndo(tgl);
        labelHariIni.textContent = infoTgl.tanggalSaja;
        chartSubtitle.textContent = 'Aktivitas per jam — ' + infoTgl.lengkap;

        fetch(urlHarian + '?date=' + tgl)
            .then(r => r.json())
            .then(json => renderCharts(json, 'line'))
            .catch(() => console.error('Gagal muat data harian.'));
    }

    function loadBulanan(monthStr) {
        const bln = monthStr || currentMonth;
        currentMonth = bln;
        inputBulan.value = bln;

        const infoBulan = formatBulanIndo(bln);
        labelBulanIni.textContent = infoBulan;
        chartSubtitle.textContent = 'Aktivitas per minggu — ' + infoBulan;

        const [year, month] = bln.split('-');

        fetch(urlBulanan + '?year=' + year + '&month=' + month)
            .then(r => r.json())
            .then(json => renderCharts(json, 'bar'))
            .catch(() => console.error('Gagal muat data bulanan.'));
    }

    /* ── Event Listeners Input ─────────────────────────────── */
    inputTanggal.addEventListener('change', function () {
        if (!this.value) return;
        // Jika sedang mode 'Lihat Semua', kembalikan ke mode harian otomatis jika merubah tanggal
        if (isShowingAll) {
            isShowingAll = false;
            ctrlHarian.style.display = '';
            ctrlBulanan.style.display = 'none';
            btnLihatSemua.innerHTML = '<i class="bi bi-calendar-day me-1"></i> Hari Ini';
        }
        loadHarian(this.value);
    });

    inputBulan.addEventListener('change', function () {
        if (!this.value) return;
        loadBulanan(this.value);
    });

    /* ── Toggle Lihat Semua ───────────────────────────────────────── */
    btnLihatSemua.addEventListener('click', function () {
        isShowingAll = !isShowingAll;
        
        if (isShowingAll) {
            // Tampilkan Bulanan
            ctrlHarian.style.display = 'none';
            ctrlBulanan.style.display = '';
            btnLihatSemua.innerHTML = '<i class="bi bi-arrows-fullscreen me-1"></i> Perbulan';
            loadBulanan(currentMonth);
        } else {
            // Kembali ke Harian
            ctrlHarian.style.display = '';
            ctrlBulanan.style.display = 'none';
            btnLihatSemua.innerHTML = '<i class="bi bi-calendar-day me-1"></i> Hari Ini';
            loadHarian(currentDate);
        }
    });

    // Load default: hari ini
    loadHarian(currentDate);
});
</script>
@endpush

@endsection

