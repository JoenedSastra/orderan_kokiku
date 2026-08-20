@extends('layouts.app')
@section('title', 'Barang Masuk Harian')
@section('content')

<script>
(function () {
    const params = new URLSearchParams(window.location.search);
    const sengajaKembali = params.has('dari'); // Admin sengaja klik "Kembali" dari grid.

    // Auto-lompat ke grid divisi terakhir HANYA kalau admin datang dari salah
    // satu halaman Stock (Gudang Utama, Gudang Resto, Kasir, Kitchen) DAN
    // memang sebelumnya sedang mengisi divisi tertentu (bukan lagi di hub).
    let asalDariHalamanStock = false;
    try {
        const referrer = new URL(document.referrer);
        const halamanStock = ['/admin/stock', '/admin/stock-kasir-kitchen'];
        asalDariHalamanStock = halamanStock.includes(referrer.pathname);
    } catch (e) {
        asalDariHalamanStock = false; // Tidak ada referrer (tab baru / bookmark, dsb).
    }

    const validLokasi = @json(collect($lokasiList)->pluck('key'));
    const lastLokasi  = localStorage.getItem('kk_last_lokasi');

    const bolehLompat = !sengajaKembali && asalDariHalamanStock
        && lastLokasi && validLokasi.includes(lastLokasi);

    if (bolehLompat) {
        window.location.replace('{{ url('/admin/stock-masuk/tambah') }}/' + lastLokasi);
        return;
    }

    // Hub benar-benar ditampilkan (bukan auto-lompat) — hapus "ingatan" divisi
    // terakhir. Jadi selama admin belum klik salah satu kotak divisi lagi,
    // menu Stock manapun yang dibuka lalu balik ke sini akan tetap di hub.
    localStorage.removeItem('kk_last_lokasi');
})();
</script>

<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header flex-wrap gap-2">
    <h2 class="h5 mb-1">Barang Masuk Harian</h2>

</div>

<style>
/* ===== Barang Masuk Cards ===== */
.kk-masuk-card {
    position: relative;
    border-radius: 24px;
    padding: 2rem 1.75rem 1.6rem;
    overflow: hidden;
    cursor: pointer;
    transition: box-shadow 0.25s ease;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    height: 210px;
    border: none !important;
}

/* Hover: shadow saja, TIDAK bergerak */
.kk-masuk-card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.22) !important;
}

/* Klik: tidak ada efek sama sekali */
.kk-masuk-card:active {
    transform: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.18) !important;
    transition: none;
}

/* ── Icon box ── */
.kk-masuk-card .card-icon-wrap {
    position: relative;
    z-index: 2;
    width: 64px; height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.22);
    border: 1.5px solid rgba(255,255,255,0.35);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.9rem;
    color: #fff;
    margin-bottom: 1.1rem;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.4);
}

/* ── Text ── */
.kk-masuk-card .card-title {
    position: relative; z-index: 2;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 800;
    margin-bottom: 0.35rem;
    letter-spacing: -0.01em;
    text-shadow: 0 1px 4px rgba(0,0,0,0.12);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kk-masuk-card .card-action {
    position: relative; z-index: 2;
    color: rgba(255,255,255,0.85);
    font-size: 0.8rem;
    font-weight: 500;
    display: flex; align-items: center; gap: 5px;
    margin-top: 0.25rem;
}

/* ── Solid flat colors per divisi ── */
.kk-card-gudang-utama {
    background: #ea580c;
    box-shadow: 0 6px 20px rgba(234,88,12,0.40);
}
.kk-card-gudang-resto {
    background: #16a34a;
    box-shadow: 0 6px 20px rgba(22,163,74,0.40);
}
.kk-card-kasir {
    background: #e11d48;
    box-shadow: 0 6px 20px rgba(225,29,72,0.40);
}
.kk-card-kitchen {
    background: #d97706;
    box-shadow: 0 6px 20px rgba(217,119,6,0.40);
}

/* ── Responsive ── */
@media (max-width: 576px) {
    .kk-masuk-card { height: 185px; padding: 1.35rem 1.1rem 1.2rem; border-radius: 18px; }
    .kk-masuk-card .card-icon-wrap { width: 50px; height: 50px; font-size: 1.4rem; border-radius: 12px; margin-bottom: 0.75rem; }
    .kk-masuk-card .card-title { font-size: 0.9rem; }
    .kk-masuk-card .card-action { font-size: 0.72rem; }
}
</style>

@php
$kartuDivisi = [
    ['key'=>'gudang_utama', 'label'=>'Gudang Utama', 'class'=>'kk-card-gudang-utama', 'icon'=>'bi-building-fill'],
    ['key'=>'gudang_resto', 'label'=>'Gudang Resto',  'class'=>'kk-card-gudang-resto',  'icon'=>'bi-shop-window'],
    ['key'=>'kasir',        'label'=>'Kasir',          'class'=>'kk-card-kasir',          'icon'=>'bi-cash-coin'],
    ['key'=>'kitchen',      'label'=>'Kitchen',        'class'=>'kk-card-kitchen',        'icon'=>'kitchen'],
];
@endphp

<div class="row g-3 g-md-4">
    @foreach($kartuDivisi as $kartu)
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.stock_in.create', ['lokasi' => $kartu['key']]) }}"
           class="kk-masuk-card {{ $kartu['class'] }}"
           onclick="localStorage.setItem('kk_last_lokasi','{{ $kartu['key'] }}')">

            {{-- Icon --}}
            <div class="card-icon-wrap">
                @if($kartu['icon'] === 'kitchen')
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 100 100" fill="white">
                        <path d="M28,62 C18,62 10,52 10,41 C10,30 18,21 28,20 C30,13 39,8 50,8 C61,8 70,13 72,20 C82,21 90,30 90,41 C90,52 82,62 72,62 Z"/>
                        <rect x="22" y="62" width="56" height="13" rx="4"/>
                        <rect x="18" y="75" width="64" height="8" rx="4"/>
                    </svg>
                @else
                    <i class="bi {{ $kartu['icon'] }}"></i>
                @endif
            </div>

            {{-- Label --}}
            <div class="card-title">{{ $kartu['label'] }}</div>
            <div class="card-action">
                <i class="bi bi-pencil-square"></i> Catat barang masuk
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Info Paragraphs --}}
<div class="mt-4 d-flex flex-column gap-3">

    {{-- Paragraf 1: Info biru --}}
    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: var(--kk-surface); border: 1px solid var(--kk-border);">
        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width:38px; height:38px; background:#dbeafe; min-width:38px;">
            <i class="bi bi-info-circle-fill" style="color:#2563eb; font-size:1.1rem;"></i>
        </div>
        <p class="mb-0 small" style="color: var(--kk-text); line-height:1.65;">
            Silakan input barang yang datang dan ingin dimasukkan sesuai devisi masing-masing untuk memastikan stok selalu akurat.
            Proses ini membantu setiap bagian (Gudang Utama, Gudang Resto, Kasir, dan Kitchen) dalam mencatat penerimaan barang
            secara terstruktur dan real-time.
        </p>
    </div>

    {{-- Paragraf 2: Centang hijau --}}
    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: var(--kk-surface); border: 1px solid var(--kk-border);">
        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width:38px; height:38px; background:#dcfce7; min-width:38px;">
            <i class="bi bi-check-circle-fill" style="color:#16a34a; font-size:1.1rem;"></i>
        </div>
        <p class="mb-0 small" style="color: var(--kk-text); line-height:1.65;">
            Pastikan data yang diinput lengkap dan benar, seperti nama barang, jumlah, satuan, tanggal masuk, dan keterangan lainnya.
            Data yang akurat akan mempengaruhi laporan stok dan ketersediaan barang di setiap devisi, sehingga memudahkan
            perencanaan dan pengendalian persediaan.
        </p>
    </div>

    {{-- Paragraf 3: Ikon oranye --}}
    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: var(--kk-surface); border: 1px solid var(--kk-border);">
        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width:38px; height:38px; background:#ffedd5; min-width:38px;">
            <i class="bi bi-clipboard2-check-fill" style="color:#ea580c; font-size:1.1rem;"></i>
        </div>
        <p class="mb-0 small" style="color: var(--kk-text); line-height:1.65;">
            Setiap barang yang masuk akan otomatis menambah stok dan tercatat dalam sistem untuk memudahkan pengecekan,
            monitoring, serta pelaporan harian, mingguan, maupun bulanan. Hal ini juga membantu mengurangi risiko selisih stok
            dan meningkatkan efisiensi operasional di seluruh bagian.
        </p>
    </div>

</div>

@endsection
