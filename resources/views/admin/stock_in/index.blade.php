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

<div class="row g-3">
    @foreach($lokasiList as $lokasi)
    <div class="col-6 col-lg-3">
        <a href="{{ route('admin.stock_in.create', ['lokasi' => $lokasi['key']]) }}" class="text-decoration-none">
            <div class="kk-stat-card {{ $lokasi['gradient'] }}" style="cursor:pointer;">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="kk-stat-icon"><i class="bi {{ $lokasi['icon'] }}"></i></div>
                </div>
                <div class="kk-stat-value">{{ $lokasi['total'] }}</div>
                <div class="kk-stat-label">{{ $lokasi['label'] }}</div>
                <div class="small mt-1" style="color:rgba(255,255,255,0.85);">
                    <i class="bi bi-plus-circle"></i> Catat barang masuk
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@endsection
