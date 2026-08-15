@extends('layouts.app')
@section('title', 'Stok Barang - ' . $title)
@section('content')


<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2 class="h5 mb-0">Stok Barang {{ $title }}</h2>
    <div class="d-flex align-items-center gap-2 flex-nowrap">
        @if($title === 'Gudang Utama')
        <button type="button" class="btn btn-sm text-white text-nowrap" style="background:#2563eb;" data-bs-toggle="modal" data-bs-target="#modalKirimBarang">
            <i class="bi bi-send-fill"></i> Kirim Barang
        </button>
        <a href="{{ route('admin.stock.riwayat_terkirim') }}" class="btn btn-sm text-white text-nowrap" style="background:#16a34a;">
            <i class="bi bi-clock-history"></i> Riwayat Terkirim
        </a>
        @endif
        <div class="kk-search-box" style="min-width:180px;">
            <i class="bi bi-search"></i>
            <input type="text" name="kk_search" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang..." autocomplete="off">
        </div>
    </div>
</div>

<div class="kk-stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Hari, Jam, &amp; Tanggal</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Master</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                @php $aktivitas = $item->latestMasukActivity(); @endphp
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center">{{ $aktivitas?->created_at?->translatedFormat('l, d M Y H:i') ?? '-' }}</td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $item->name }}</td>
                    <td class="text-center">{{ $item->totalStock() }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-center"><span class="badge" style="background:#bfdbfe;color:#1d4ed8;font-weight:600;">{{ $item->masterLocationLabel() }}</span></td>
                    <td class="text-center">{{ $aktivitas?->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $aktivitas?->user?->role?->name ?? '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="p-3">{{ $items->links() }}</div>
    @endif
</div>

@if($title === 'Gudang Utama')
<div class="modal fade" id="modalKirimBarang" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <form action="{{ route('admin.stock.kirim') }}" method="POST" id="formKirimBarang">
                @csrf

                {{-- Header gradient --}}
                <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#1d4ed8 0%,#2563eb 60%,#3b82f6 100%);padding:1.4rem 1.5rem 2.5rem;">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold text-white" style="font-size:1.05rem;">Kirim Barang</h5>
                        <small style="color:rgba(255,255,255,.75);font-size:.78rem;">Dari Gudang Utama ke Divisi Lain</small>
                    </div>
                </div>

                {{-- Body --}}
                <div class="modal-body px-4" style="margin-top:-1.2rem;padding-top:0;">

                    {{-- Info card --}}
                    <div class="d-flex align-items-center gap-2 p-3 mb-3 rounded-3"
                         style="background:#eff6ff;border:1px solid #bfdbfe;">
                        <i class="bi bi-info-circle-fill" style="color:#2563eb;font-size:1rem;flex-shrink:0;"></i>
                        <small style="color:#1e40af;line-height:1.4;">
                            Barang yang dikirim akan <strong>mengurangi stok</strong> Gudang Utama dan
                            <strong>menambah stok</strong> divisi tujuan secara otomatis.
                        </small>
                    </div>

                    {{-- Pilih Barang --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                            <i class="bi bi-box-seam me-1" style="color:#2563eb;"></i>Pilih Barang
                        </label>
                        <select name="item_id" id="selectBarang"
                                class="form-select @error('item_id') is-invalid @enderror"
                                style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;" required>
                            <option value="">Pilih Barang</option>
                            @foreach($itemsGudangUtama as $item)
                            <option value="{{ $item->id }}" data-stok="{{ $item->stokGudang() }}" data-unit="{{ $item->unit }}">
                                {{ $item->name }} — Stok: {{ $item->stokGudang() }} {{ $item->unit }}
                            </option>
                            @endforeach
                        </select>
                        @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        {{-- Stok tersedia badge --}}
                        <div id="stokInfo" class="mt-2 d-none">
                            <span class="badge px-3 py-2" style="background:#dbeafe;color:#1d4ed8;font-size:.8rem;border-radius:8px;">
                                <i class="bi bi-layers-half me-1"></i>
                                Stok tersedia: <strong id="stokText">-</strong>
                            </span>
                        </div>
                    </div>

                    {{-- Kirim ke --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                            <i class="bi bi-geo-alt me-1" style="color:#2563eb;"></i>Kirim ke
                        </label>
                        <select name="destination" id="selectKirimKe"
                                class="form-select @error('destination') is-invalid @enderror"
                                style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;" required>
                            <option value="">Pilih Divisi</option>
                            <option value="gudang_resto">🏠 Gudang Resto</option>
                            <option value="kasir">💰 Kasir</option>
                            <option value="kitchen">🍳 Kitchen</option>
                        </select>
                        @error('destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Jumlah & Keterangan dalam 1 baris --}}
                    <div class="row g-3">
                        <div class="col-5">
                            <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                                <i class="bi bi-123 me-1" style="color:#2563eb;"></i>Jumlah
                            </label>
                            <input type="number" name="quantity" min="1" value="1"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;text-align:center;" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-7">
                            <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                                <i class="bi bi-tag me-1" style="color:#2563eb;"></i>Keterangan
                            </label>
                            <select name="keterangan" id="selectKeterangan"
                                    class="form-select @error('keterangan') is-invalid @enderror"
                                    style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;" required>
                                <option value="">Pilih Keterangan</option>
                                <option value="Kirim di Gudang Resto">Kirim di Gudang Resto</option>
                                <option value="Kirim di Kasir">Kirim di Kasir</option>
                                <option value="Kirim di Kitchen">Kirim di Kitchen</option>
                            </select>
                            @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                    <button type="button" class="btn btn-light fw-semibold px-4" data-bs-dismiss="modal"
                            style="border-radius:10px;font-size:.875rem;border:1px solid #e5e7eb;">
                        Batal
                    </button>
                    <button type="submit" class="btn text-white fw-semibold px-4 flex-grow-1"
                            style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);border:none;border-radius:10px;font-size:.875rem;transition:.2s;"
                            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        Kirim Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const selectBarang = document.getElementById('selectBarang');
    const stokInfo     = document.getElementById('stokInfo');
    const stokText     = document.getElementById('stokText');

    if (selectBarang) {
        selectBarang.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const stok = opt.dataset.stok;
            const unit = opt.dataset.unit;
            if (stok !== undefined && this.value) {
                stokText.textContent = stok + ' ' + unit;
                stokInfo.classList.remove('d-none');
            } else {
                stokInfo.classList.add('d-none');
            }
        });
    }

    // Auto-sync "Keterangan" mengikuti pilihan "Kirim ke", supaya keterangan
    // yang tersimpan di divisi tujuan selalu sesuai divisi yang sesungguhnya
    // dipilih (server tetap jadi penjamin akhir lewat $labelTujuan).
    const selectKirimKe    = document.getElementById('selectKirimKe');
    const selectKeterangan = document.getElementById('selectKeterangan');
    const kirimKeToKeterangan = {
        gudang_resto: 'Kirim di Gudang Resto',
        kasir: 'Kirim di Kasir',
        kitchen: 'Kirim di Kitchen',
    };

    if (selectKirimKe && selectKeterangan) {
        selectKirimKe.addEventListener('change', function () {
            selectKeterangan.value = kirimKeToKeterangan[this.value] || '';
        });
    }
})();
</script>
@endpush
@endif
@endsection
