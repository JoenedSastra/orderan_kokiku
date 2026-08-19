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
    <a href="{{ route('admin.stock_in.riwayat') }}" class="btn btn-sm text-white" style="background:#16a34a;">
        <i class="bi bi-clock-history"></i> Riwayat Hari Ini
    </a>
</div>

<style>
.kk-masuk-card {
    position: relative;
    border-radius: 16px;
    padding: 1.75rem 1.5rem;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    text-decoration: none;
    display: block;
    min-height: 180px;
}
.kk-masuk-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25) !important;
}
.kk-masuk-card .bg-orb {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    pointer-events: none;
}
.kk-masuk-card .bg-orb-1 {
    width: 140px; height: 140px;
    top: -40px; right: -40px;
}
.kk-masuk-card .bg-orb-2 {
    width: 80px; height: 80px;
    bottom: -20px; left: -20px;
}
.kk-masuk-card .card-icon-wrap {
    width: 60px; height: 60px;
    border-radius: 14px;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.7rem;
    color: #fff;
    margin-bottom: 1rem;
    backdrop-filter: blur(4px);
    transition: background 0.2s;
}
.kk-masuk-card:hover .card-icon-wrap {
    background: rgba(255,255,255,0.3);
}
.kk-masuk-card .card-title {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
    letter-spacing: 0.3px;
}
.kk-masuk-card .card-action {
    color: rgba(255,255,255,0.85);
    font-size: 0.82rem;
    display: flex; align-items: center; gap: 5px;
}
</style>

<div class="row g-3">
    @foreach($lokasiList as $lokasi)
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.stock_in.create', ['lokasi' => $lokasi['key']]) }}" class="kk-masuk-card kk-stat-card {{ $lokasi['gradient'] }}">
            {{-- Decorative background orbs --}}
            <div class="bg-orb bg-orb-1"></div>
            <div class="bg-orb bg-orb-2"></div>

            {{-- Icon --}}
            <div class="card-icon-wrap">
                @if($lokasi['key'] === 'kitchen')
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 100 100" fill="white">
                        <!-- Topi koki (Toque Blanche) -->
                        <!-- Tubuh utama topi: berbentuk silinder bulat di atas -->
                        <path d="
                            M 28,62
                            C 18,62 10,52 10,41
                            C 10,30 18,21 28,20
                            C 30,13 39,8 50,8
                            C 61,8 70,13 72,20
                            C 82,21 90,30 90,41
                            C 90,52 82,62 72,62
                            Z
                        "/>
                        <!-- Pita bawah topi -->
                        <rect x="22" y="62" width="56" height="13" rx="4"/>
                        <!-- Lipatan bawah -->
                        <rect x="18" y="75" width="64" height="8" rx="4"/>
                    </svg>
                @else
                    <i class="bi {{ $lokasi['icon'] }}"></i>
                @endif
            </div>

            {{-- Label --}}
            <div class="card-title">{{ $lokasi['label'] }}</div>
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
