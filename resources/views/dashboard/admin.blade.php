@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')

{{-- Hero Greeting Card --}}
<div class="kk-hero-card mb-4 position-relative" id="heroBgContainer" onclick="document.getElementById('heroBgInput').click()" style="cursor: pointer; background-size: cover; background-position: center; transition: background-image 0.3s ease-in-out;" title="Klik untuk mengganti gambar latar">
    {{-- Overlay to ensure text readability if background is bright --}}
    <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.6) 100%); border-radius: inherit; z-index: 0; pointer-events: none;"></div>
    
    <div class="row align-items-center position-relative" style="z-index: 1;">
        <div class="col-12">
            <div class="kk-hero-greeting">PANEL ADMINISTRATOR</div>
            <div class="kk-hero-name">Halo, {{ $user->name }}!</div>
            <div class="kk-hero-sub">Selamat datang di pusat kendali inventaris Kokiku.</div>
            <div class="kk-hero-badge">
                <i class="bi bi-calendar3"></i>
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </div>
</div>
<input type="file" class="d-none" id="heroBgInput" accept="image/*">


{{-- Aksi Cepat --}}
<div class="kk-stat-card mb-4" style="background-color: var(--kk-surface-2);">
    <div class="kk-section-header mb-3">
        <div class="kk-section-title">
            <i class="bi bi-lightning-charge"></i> Aksi Cepat
        </div>
    </div>
    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <a href="{{ route('admin.stock_in.index') }}" class="kk-quick-action kk-action-green">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>Barang Masuk Harian</span>
            </a>
        </div>
        <div class="col-12 col-lg-4">
            <a href="{{ route('admin.stock.index', ['filter' => 'gudang_utama']) }}" class="kk-quick-action kk-action-red">
                <i class="bi bi-box-arrow-up"></i>
                <span>Kirim Barang Gudang</span>
            </a>
        </div>
        <div class="col-12 col-lg-4">
            <a href="{{ route('admin.data_stock.index') }}" class="kk-quick-action kk-action-orange">
                <i class="bi bi-box-seam"></i>
                <span>Data Stock Devisi</span>
            </a>
        </div>
    </div>
</div>




{{-- Baris 2: Grafik Aktivitas & Donut Kategori --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="kk-stat-card h-100 mb-0">
            <div class="kk-section-header" style="flex-wrap:wrap; gap:0.5rem;">
                <div class="kk-section-title">
                    <i class="bi bi-graph-up-arrow"></i> Grafik Aktivitas
                </div>
                {{-- Tab switcher --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div id="ctrlHarian" class="kk-chart-ctrl position-relative" style="display:inline-flex; align-items:center;">
                        <div id="badgeTanggal" class="kk-chart-date-badge d-flex align-items-center" style="background:var(--kk-surface-2); border:1px solid var(--kk-border); border-radius:8px; padding:0.4rem 0.75rem; font-size:0.875rem; font-weight:500; cursor:pointer; transition:all 0.2s;" title="Klik untuk pilih tanggal">
                            <i class="bi bi-calendar-check me-1 text-primary"></i>
                            <span id="labelHariIni">{{ now()->translatedFormat('d F Y') }}</span>
                            <i class="bi bi-chevron-down ms-2 text-muted" style="font-size:0.7rem;"></i>
                        </div>
                        <input type="date" id="inputTanggalChart" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2;" title="Pilih tanggal">
                    </div>

                    <div id="ctrlBulanan" class="kk-chart-ctrl position-relative" style="display:none; align-items:center;">
                        <div id="badgeBulan" class="kk-chart-date-badge d-flex align-items-center" style="background:var(--kk-surface-2); border:1px solid var(--kk-border); border-radius:8px; padding:0.4rem 0.75rem; font-size:0.875rem; font-weight:500; cursor:pointer; transition:all 0.2s;" title="Klik untuk pilih bulan">
                            <i class="bi bi-calendar-month me-1 text-primary"></i>
                            <span id="labelBulanIni">{{ now()->translatedFormat('F Y') }}</span>
                            <i class="bi bi-chevron-down ms-2 text-muted" style="font-size:0.7rem;"></i>
                        </div>
                        <input type="month" id="inputBulanChart" value="{{ now()->format('Y-m') }}" max="{{ now()->format('Y-m') }}" style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2;" title="Pilih bulan">
                    </div>

                    <button id="btnLihatSemua" class="btn btn-sm" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius:8px; padding:0.4rem 0.75rem; font-size:0.875rem; font-weight:500; transition: all 0.2s;">
                        <i class="bi bi-calendar-day me-1"></i> Hari Ini
                    </button>
                </div>
            </div>

            <div id="chartSubtitle" style="font-size:0.8rem; color:var(--kk-text-muted); margin-bottom:1rem; margin-top:-0.25rem; padding-left:0.1rem;">
                Menampilkan data hari ini
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <div class="h-100 d-flex flex-column" style="background:var(--kk-surface-2); border-radius:var(--kk-radius-sm); padding:1rem; min-height: 250px;">
                        <div id="custom-legend-aktivitas" class="d-flex justify-content-center gap-4 mb-3"></div>
                        <div class="flex-grow-1 position-relative" style="height: 250px;">
                            <canvas id="chartAktivitas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Donut Chart Devisi --}}
    <div class="col-lg-4">
        <div class="kk-stat-card h-100">
            <div class="kk-section-header">
                <div class="kk-section-title">
                    <i class="bi bi-pie-chart-fill"></i> Barang per Devisi
                </div>
            </div>
            <div class="mt-3">
                <canvas id="chartKategori" height="250"></canvas>
            </div>
        </div>
    </div>
</div>


{{-- Baris 4: Peringatan Stok Rendah --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="kk-stat-card h-100 flex-fill">
            <div class="kk-section-header mb-3">
                <div class="kk-section-title text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Rendah
                </div>
                <span class="badge bg-danger rounded-pill">{{ $stokRendah }}</span>
            </div>
            @if($stokRendah > 0)
                @php
                    $divisions = [
                        \App\Models\Item::MASTER_GUDANG_UTAMA => 'Gudang Utama',
                        \App\Models\Item::MASTER_GUDANG_RESTO => 'Gudang Resto',
                        \App\Models\Item::MASTER_KASIR => 'Kasir',
                        \App\Models\Item::MASTER_KITCHEN => 'Kitchen',
                    ];
                @endphp
                <div class="row g-3">
                    @foreach($divisions as $key => $label)
                        @php
                            $divItems = \App\Models\Item::whereHas('stockIns')->get()->filter(fn($i) => $i->master_location === $key && $i->stokByLocation($key) <= 10);
                        @endphp
                        <div class="col-md-6">
                            <div class="card h-100 kk-card border" style="border-radius: 10px;">
                                <div class="card-header border-0 d-flex justify-content-between align-items-center" style="background-color: transparent; border-bottom: 1px solid rgba(128,128,128,0.1) !important;">
                                    <h6 class="mb-0 fw-bold" style="color:var(--kk-text);">{{ $label }}</h6>
                                    @if($divItems->count() > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $divItems->count() }}</span>
                                    @else
                                        <span class="badge bg-success rounded-pill">Aman</span>
                                    @endif
                                </div>
                                <div class="card-body p-3">
                                    @if($divItems->count() > 0)
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($divItems as $item)
                                                <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: var(--kk-danger-soft); border: 1px solid rgba(239, 68, 68, 0.2);">
                                                    <div>
                                                        <div class="fw-semibold" style="font-size:0.9rem; color:var(--kk-text);">
                                                            {{ $item->name }} 
                                                        </div>
                                                        <div style="font-size:0.8rem; color:var(--kk-danger);">Stock: {{ $item->stokByLocation($key) }} {{ $item->unit }}</div>
                                                    </div>
                                                    <a href="{{ route('admin.data_stock.index') }}" class="btn btn-outline-danger btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; font-weight: 500;">Lihat Data</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted text-center py-3" style="font-size: 0.85rem;">
                                            <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                                            Stok aman
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted" style="font-size:0.95rem;">
                    <i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>
                    Stok semua divisi aman (di atas 10).
                </div>
            @endif
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
    let chartAktivitas = null;

    const inputTanggal  = document.getElementById('inputTanggalChart');
    const labelHariIni  = document.getElementById('labelHariIni');
    const inputBulan    = document.getElementById('inputBulanChart');
    const labelBulanIni = document.getElementById('labelBulanIni');
    const chartSubtitle = document.getElementById('chartSubtitle');
    const btnLihatSemua = document.getElementById('btnLihatSemua');
    const ctrlHarian    = document.getElementById('ctrlHarian');
    const ctrlBulanan   = document.getElementById('ctrlBulanan');

    function renderCharts(json, type = 'bar') {
        if (chartAktivitas) {
            chartAktivitas.destroy();
        }
        
        let hiddenStates = [false, false, false];
        try {
            const saved = localStorage.getItem('chartAktivitas_hidden');
            if (saved) hiddenStates = JSON.parse(saved);
        } catch(e) {}
        
        const ctx = document.getElementById('chartAktivitas');
        chartAktivitas = new window.Chart(ctx, {
            type: type,
            data: {
                labels: json.labels,
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: json.masuk,
                        hidden: hiddenStates[0],
                        backgroundColor: type === 'line' ? '#10b98133' : '#10b981',
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderRadius: type === 'bar' ? 4 : 0,
                        fill: type === 'line',
                        tension: 0.4,
                        pointRadius: type === 'line' ? 3 : 0,
                        pointHoverRadius: type === 'line' ? 6 : 0,
                    },
                    {
                        label: 'Barang Keluar',
                        data: json.keluar,
                        hidden: hiddenStates[1],
                        backgroundColor: type === 'line' ? '#ef444433' : '#ef4444',
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        borderRadius: type === 'bar' ? 4 : 0,
                        fill: type === 'line',
                        tension: 0.4,
                        pointRadius: type === 'line' ? 3 : 0,
                        pointHoverRadius: type === 'line' ? 6 : 0,
                    },
                    {
                        label: 'Stock',
                        data: json.permintaan,
                        hidden: hiddenStates[2],
                        backgroundColor: type === 'line' ? '#ff6b3533' : '#ff6b35',
                        borderColor: '#ff6b35',
                        borderWidth: 2,
                        borderRadius: type === 'bar' ? 4 : 0,
                        fill: type === 'line',
                        tension: 0.4,
                        pointRadius: type === 'line' ? 3 : 0,
                        pointHoverRadius: type === 'line' ? 6 : 0,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: { 
                    legend: { 
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9ca3af', maxRotation: 45 } }
                },
            },
        });
        
        updateCustomLegend();
    }

    function updateCustomLegend() {
        const legendContainer = document.getElementById('custom-legend-aktivitas');
        if (!legendContainer || !chartAktivitas) return;
        
        let html = '';
        const datasets = chartAktivitas.data.datasets;
        datasets.forEach((dataset, index) => {
            const isHidden = chartAktivitas.isDatasetVisible(index) === false;
            const mainColor = dataset.borderColor; 
            const bgColor = isHidden ? 'transparent' : mainColor;
            const tick = isHidden ? '' : '<i class="bi bi-check2 text-white" style="font-size:0.9rem; line-height: 1; font-weight: 900;"></i>';
            
            html += `
                <div class="d-flex align-items-center" style="cursor: pointer; user-select: none;" onclick="toggleAktivitasDataset(${index})">
                    <div style="width: 18px; height: 18px; border-radius: 50%; background-color: ${bgColor}; border: 2px solid ${mainColor}; display: flex; align-items: center; justify-content: center; margin-right: 6px; transition: all 0.2s;">
                        ${tick}
                    </div>
                    <span style="color: #9ca3af; font-size: 13px; text-decoration: none; opacity: ${isHidden ? '0.6' : '1'}; transition: all 0.2s;">${dataset.label}</span>
                </div>
            `;
        });
        legendContainer.innerHTML = html;
    }

    window.toggleAktivitasDataset = function(index) {
        if (!chartAktivitas) return;
        if (chartAktivitas.isDatasetVisible(index)) {
            chartAktivitas.hide(index);
        } else {
            chartAktivitas.show(index);
        }
        
        // Simpan ke localStorage
        const states = [
            !chartAktivitas.isDatasetVisible(0),
            !chartAktivitas.isDatasetVisible(1),
            !chartAktivitas.isDatasetVisible(2)
        ];
        localStorage.setItem('chartAktivitas_hidden', JSON.stringify(states));
        
        updateCustomLegend();
    };

    // Donut Chart Devisi
    const ctxKategori = document.getElementById('chartKategori');
    if(ctxKategori) {
        new window.Chart(ctxKategori, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($donutLabels) !!},
                datasets: [{
                    data: {!! json_encode($donutData) !!},
                    backgroundColor: [
                        '#ff6b35', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: 'rgba(255,255,255,0.05)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#9ca3af',
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
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

    /* ── Ganti Latar Belakang (Hero Card) ────────────────────── */
    const heroBgInput = document.getElementById('heroBgInput');
    const heroBgContainer = document.getElementById('heroBgContainer');
    
    // Muat dari localStorage jika ada
    const savedBg = localStorage.getItem('heroBgImage');
    if (savedBg) {
        heroBgContainer.style.backgroundImage = `url(${savedBg})`;
    }

    if (heroBgInput) {
        heroBgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const base64Image = event.target.result;
                    heroBgContainer.style.backgroundImage = `url(${base64Image})`;
                    try {
                        localStorage.setItem('heroBgImage', base64Image);
                    } catch (err) {
                        console.error("File terlalu besar untuk localStorage", err);
                        alert("Gambar ini terlalu besar untuk disimpan di memori browser. Gambar akan hilang saat direfresh.");
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush

@endsection

