@extends('layouts.app')
@section('title', 'Data Stock 4 Devisi')

@section('content')
<style>
    :root {
        --ds-border: #000;
    }
    [data-bs-theme="dark"] {
        --ds-border: #a1a1aa; /* abu-abu terang agar tidak terlalu mencolok di dark mode */
    }
</style>
<div class="container-fluid mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="h5 mb-0">Data Stock Barang Per Devisi</h2>
        <div class="d-flex align-items-center gap-2">
            {{-- Filter Devisi --}}
            <select id="filterDevisi" class="form-select form-select-sm" style="width:auto;">
                <option value="all">Semua Devisi</option>
                <option value="gudang-utama">Gudang Utama</option>
                <option value="gudang-resto">Gudang Resto</option>
                <option value="kasir">Kasir</option>
                <option value="kitchen">Kitchen</option>
            </select>
            {{-- Search --}}
            <div class="kk-search-box" style="min-width:180px;">
                <i class="bi bi-search"></i>
                <input type="text" id="searchDataStock" class="form-control form-control-sm" placeholder="Cari nama barang..." autocomplete="off">
            </div>
        </div>
    </div>

    <div class="row g-4" id="tabelDevisiWrapper">
        <!-- Gudang Utama -->
        <div class="col-md-6" data-devisi="gudang-utama">
            <div class="card kk-card" style="border-radius: 0;">
                <div class="card-header text-white text-center fw-bold" style="background-color: #2563eb; border-radius: 0;">
                    Tabel Gudang Utama
                </div>
                <div class="card-body p-0" style="border-left: 1px solid var(--ds-border); border-right: 1px solid var(--ds-border); border-bottom: 1px solid var(--ds-border);">
                    <table class="table table-hover mb-0" style="table-layout: fixed; width: 100%; border: 1px solid var(--ds-border) !important;">
                            <thead class="text-center">
                                <tr>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 10%; border: 1px solid var(--ds-border) !important;">NO</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; border: 1px solid var(--ds-border) !important;">NAMA BARANG</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 25%; border: 1px solid var(--ds-border) !important;">
                                        <div style="display: block; text-align: center; width: 100%;">STOCK</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gudangUtama as $index => $item)
                                <tr>
                                    <td class="text-center align-middle fw-bold" style="border: 1px solid var(--ds-border) !important;">{{ $index + 1 }}</td>
                                    <td class="align-middle" style="border: 1px solid var(--ds-border) !important;">{{ $item->name }}</td>
                                    <td class="text-center align-middle" style="color:#0284c7 !important; border: 1px solid var(--ds-border) !important;">{{ $item->stock }} {{ $item->unit }}</td>
                                </tr>
                                @endforeach
                                @if($gudangUtama->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center" style="border: 1px solid var(--ds-border) !important;">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gudang Resto -->
        <div class="col-md-6" data-devisi="gudang-resto">
            <div class="card kk-card" style="border-radius: 0;">
                <div class="card-header text-white text-center fw-bold" style="background-color: #2563eb; border-radius: 0;">
                    Tabel Gudang Resto
                </div>
                <div class="card-body p-0" style="border-left: 1px solid var(--ds-border); border-right: 1px solid var(--ds-border); border-bottom: 1px solid var(--ds-border);">
                    <table class="table table-hover mb-0" style="table-layout: fixed; width: 100%; border: 1px solid var(--ds-border) !important;">
                            <thead class="text-center">
                                <tr>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 10%; border: 1px solid var(--ds-border) !important;">NO</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; border: 1px solid var(--ds-border) !important;">NAMA BARANG</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 25%; border: 1px solid var(--ds-border) !important;">
                                        <div style="display: block; text-align: center; width: 100%;">STOCK</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gudangResto as $index => $item)
                                <tr>
                                    <td class="text-center align-middle fw-bold" style="border: 1px solid var(--ds-border) !important;">{{ $index + 1 }}</td>
                                    <td class="align-middle" style="border: 1px solid var(--ds-border) !important;">{{ $item->name }}</td>
                                    <td class="text-center align-middle" style="color:#0284c7 !important; border: 1px solid var(--ds-border) !important;">{{ $item->stock }} {{ $item->unit }}</td>
                                </tr>
                                @endforeach
                                @if($gudangResto->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center" style="border: 1px solid var(--ds-border) !important;">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kasir -->
        <div class="col-md-6" data-devisi="kasir">
            <div class="card kk-card" style="border-radius: 0;">
                <div class="card-header text-white text-center fw-bold" style="background-color: #2563eb; border-radius: 0;">
                    Tabel Kasir
                </div>
                <div class="card-body p-0" style="border-left: 1px solid var(--ds-border); border-right: 1px solid var(--ds-border); border-bottom: 1px solid var(--ds-border);">
                    <table class="table table-hover mb-0" style="table-layout: fixed; width: 100%; border: 1px solid var(--ds-border) !important;">
                            <thead class="text-center">
                                <tr>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 10%; border: 1px solid var(--ds-border) !important;">NO</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; border: 1px solid var(--ds-border) !important;">NAMA BARANG</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 25%; border: 1px solid var(--ds-border) !important;">
                                        <div style="display: block; text-align: center; width: 100%;">STOCK</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kasir as $index => $item)
                                <tr>
                                    <td class="text-center align-middle fw-bold" style="border: 1px solid var(--ds-border) !important;">{{ $index + 1 }}</td>
                                    <td class="align-middle" style="border: 1px solid var(--ds-border) !important;">{{ $item->name }}</td>
                                    <td class="text-center align-middle" style="color:#0284c7 !important; border: 1px solid var(--ds-border) !important;">{{ $item->stock }} {{ $item->unit }}</td>
                                </tr>
                                @endforeach
                                @if($kasir->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center" style="border: 1px solid var(--ds-border) !important;">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kitchen -->
        <div class="col-md-6" data-devisi="kitchen">
            <div class="card kk-card" style="border-radius: 0;">
                <div class="card-header text-white text-center fw-bold" style="background-color: #2563eb; border-radius: 0;">
                    Tabel Kitchen
                </div>
                <div class="card-body p-0" style="border-left: 1px solid var(--ds-border); border-right: 1px solid var(--ds-border); border-bottom: 1px solid var(--ds-border);">
                    <table class="table table-hover mb-0" style="table-layout: fixed; width: 100%; border: 1px solid var(--ds-border) !important;">
                            <thead class="text-center">
                                <tr>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 10%; border: 1px solid var(--ds-border) !important;">NO</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; border: 1px solid var(--ds-border) !important;">NAMA BARANG</th>
                                    <th class="text-center align-middle" style="background-color: #ffb703 !important; color: #000 !important; width: 25%; border: 1px solid var(--ds-border) !important;">
                                        <div style="display: block; text-align: center; width: 100%;">STOCK</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kitchen as $index => $item)
                                <tr>
                                    <td class="text-center align-middle fw-bold" style="border: 1px solid var(--ds-border) !important;">{{ $index + 1 }}</td>
                                    <td class="align-middle" style="border: 1px solid var(--ds-border) !important;">{{ $item->name }}</td>
                                    <td class="text-center align-middle" style="color:#0284c7 !important; border: 1px solid var(--ds-border) !important;">{{ $item->stock }} {{ $item->unit }}</td>
                                </tr>
                                @endforeach
                                @if($kitchen->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center" style="border: 1px solid var(--ds-border) !important;">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const searchInput  = document.getElementById('searchDataStock');
    const filterSelect = document.getElementById('filterDevisi');
    const cards        = document.querySelectorAll('#tabelDevisiWrapper [data-devisi]');

    function applyFilter() {
        const keyword = searchInput.value.toLowerCase().trim();
        const devisi  = filterSelect.value;

        cards.forEach(card => {
            const cardDevisi = card.dataset.devisi;

            // Filter devisi
            const devisiMatch = (devisi === 'all' || cardDevisi === devisi);

            if (!devisiMatch) {
                card.style.display = 'none';
                return;
            }

            // Filter nama barang per baris
            const rows = card.querySelectorAll('tbody tr');
            let hasVisible = false;
            rows.forEach(row => {
                const namaTd = row.querySelector('td:nth-child(2)');
                if (!namaTd) return;
                const nama = namaTd.textContent.toLowerCase();
                const match = nama.includes(keyword);
                row.style.display = match ? '' : 'none';
                if (match) hasVisible = true;
            });

            card.style.display = (keyword === '' || hasVisible) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilter);
    filterSelect.addEventListener('change', applyFilter);
})();
</script>
@endpush
