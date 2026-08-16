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
        @if(in_array($title, ['Gudang Utama', 'Gudang Resto']))
        <button type="button" class="btn btn-sm text-white text-nowrap" style="background:#f59e0b;" data-bs-toggle="modal" data-bs-target="#modalAturStok">
            <i class="bi bi-sliders"></i> Atur Jumlah Stock
        </button>
        @endif
        @if(in_array($title, ['Gudang Utama', 'Gudang Resto']))
        <button type="button" class="btn btn-sm text-white text-nowrap" style="background:#ef4444;" data-bs-toggle="modal" data-bs-target="#modalHapusBarang">
            <i class="bi bi-trash"></i> Hapus Barang
        </button>
        @endif
        <div class="kk-search-box" style="min-width:180px;">
            <i class="bi bi-search"></i>
            <input type="text" name="kk_search" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang..." autocomplete="off">
        </div>
    </div>
</div>

<div class="kk-stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 text-center align-middle table-sm">
            <thead class="table-light">
                <tr>
                    <th class="text-center align-middle">No</th>
                    <th class="text-center align-middle">Hari, Jam, &amp; Tanggal</th>
                    <th class="text-center align-middle">Nama Barang</th>
                    <th class="text-center align-middle" style="color:#059669;">Masuk</th>
                    <th class="text-center align-middle" style="color:#dc2626;">Keluar</th>
                    <th class="text-center align-middle" style="color:#0284c7;">Sisa</th>
                    <th class="text-center align-middle">Satuan</th>
                    <th class="text-center align-middle">Master</th>
                    <th class="text-center align-middle">Keterangan</th>
                    <th class="text-center align-middle">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                @php $aktivitas = $item->latestActivity(); @endphp
                <tr>
                    <td class="text-center align-middle"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center align-middle">{{ $aktivitas?->created_at?->translatedFormat('l, d M Y H:i') ?? '-' }}</td>
                    <td class="text-center fw-bold align-middle" data-search="nama-barang">{{ $item->name }}</td>
                    <td class="text-center text-success fw-semibold align-middle">{{ $item->masukByLocation($item->master_location) }}</td>
                    <td class="text-center text-danger fw-semibold align-middle">{{ $item->keluarByLocation($item->master_location) }}</td>
                    <td class="text-center text-primary fw-bold align-middle">{{ $item->stokByLocation($item->master_location) }}</td>
                    <td class="text-center align-middle">{{ $item->unit }}</td>
                    <td class="text-center align-middle"><span class="badge" style="background:#bfdbfe;color:#1d4ed8;font-weight:600;">{{ $item->masterLocationLabel() }}</span></td>
                    <td class="text-center align-middle">{{ $aktivitas?->keterangan ?? '-' }}</td>
                    <td class="text-center align-middle">
                        <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $aktivitas?->user?->role?->name ?? '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted py-3 align-middle">Belum ada data.</td></tr>
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
                            <option value="{{ $item->id }}" data-stok="{{ $item->stokGudangUtama() }}" data-unit="{{ $item->unit }}">
                                {{ $item->name }} — Stok: {{ $item->stokGudangUtama() }} {{ $item->unit }}
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
                            <input type="text" name="keterangan" id="inputKeterangan"
                                   class="form-control @error('keterangan') is-invalid @enderror"
                                   placeholder="Masukkan keterangan pengiriman..."
                                   style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;" required autocomplete="off">
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
})();
</script>
@endpush
@endif

{{-- Modal Atur Jumlah Stok (Semua Divisi) --}}
<div class="modal fade" id="modalAturStok" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <form action="{{ route('admin.stock.adjust') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);padding:1.4rem 1.5rem 2.5rem;">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold text-white" style="font-size:1.05rem;">Atur Jumlah Stock</h5>
                        <small style="color:rgba(255,255,255,.75);font-size:.78rem;">Penyesuaian stok manual secara massal untuk {{ $title }}</small>
                    </div>
                </div>
                <div class="modal-body px-4" style="margin-top:-1.2rem;padding-top:0;">
                    <div class="d-flex align-items-center gap-2 p-3 mb-3 rounded-3" style="background:#fef3c7;border:1px solid #fde68a;">
                        <i class="bi bi-info-circle-fill" style="color:#d97706;font-size:1rem;flex-shrink:0;"></i>
                        <small style="color:#92400e;line-height:1.4;">
                            Masukkan jumlah stok yang sebenarnya. Sistem akan otomatis menyesuaikan jumlahnya.
                        </small>
                    </div>
                    
                    <div class="table-responsive border rounded-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;width:50px;">No</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;width:35%;white-space:nowrap;">Nama Barang</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;white-space:nowrap;min-width:120px;">Stok Saat Ini</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;white-space:nowrap;min-width:140px;">Jumlah Stok Baru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allItemsForAdjust as $item)
                                <tr>
                                    <td class="text-center fw-bold" style="font-size:.875rem;">{{ $loop->iteration }}</td>
                                    <td class="text-center fw-bold" style="font-size:.875rem;">{{ $item->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-normal" style="font-size:.8rem;">
                                            {{ $item->stokByLocation($item->master_location) }} {{ $item->unit }}
                                        </span>
                                    </td>
                                    <td class="pe-3 align-middle">
                                        <div class="input-group input-group-sm mx-auto flex-nowrap" style="max-width: 160px;">
                                            <input type="number" name="new_stock[{{ $item->id }}]" class="form-control text-center" min="0" value="{{ $item->stokByLocation($item->master_location) }}" style="border-radius:6px 0 0 6px; min-width: 60px;">
                                            <span class="input-group-text bg-light text-dark" style="border-radius:0 6px 6px 0; font-size: .75rem; min-width: 60px; justify-content: center;">{{ $item->unit }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada barang untuk disesuaikan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-3 gap-2">
                    <button type="button" class="btn btn-light fw-semibold px-4" data-bs-dismiss="modal" style="border-radius:10px;font-size:.875rem;border:1px solid #e5e7eb;">Batal</button>
                    <button type="submit" class="btn text-white fw-semibold px-4 flex-grow-1" style="background:linear-gradient(135deg,#f59e0b,#d97706);border:none;border-radius:10px;font-size:.875rem;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hapus Barang (Semua Divisi) --}}
<div class="modal fade" id="modalHapusBarang" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <form action="{{ route('admin.stock.delete_items') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#ef4444 0%,#b91c1c 100%);padding:1.4rem 1.5rem 2.5rem;">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold text-white" style="font-size:1.05rem;">Hapus Barang</h5>
                        <small style="color:rgba(255,255,255,.75);font-size:.78rem;">Pilih barang yang ingin dihapus permanen dari {{ $title }}</small>
                    </div>
                </div>
                <div class="modal-body px-4" style="margin-top:-1.2rem;padding-top:0;">
                    <div class="d-flex align-items-center gap-2 p-3 mb-3 rounded-3" style="background:#fef2f2;border:1px solid #fecaca;">
                        <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:1rem;flex-shrink:0;"></i>
                        <small style="color:#991b1b;line-height:1.4;">
                            <strong>Peringatan:</strong> Menghapus barang akan menghapus secara permanen seluruh riwayat barang masuk, barang keluar, dan pesanan yang terkait dengan barang tersebut.
                        </small>
                    </div>
                    
                    <div class="table-responsive border rounded-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <input class="form-check-input" type="checkbox" id="checkAllHapus">
                                    </th>
                                    <th style="font-size:.85rem;color:#374151;white-space:nowrap;width:35%;">Nama Barang</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;white-space:nowrap;min-width:120px;">Stok Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allItemsForAdjust as $item)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input check-item-hapus" type="checkbox" name="item_ids[]" value="{{ $item->id }}">
                                    </td>
                                    <td class="fw-bold" style="font-size:.875rem;">{{ $item->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-normal" style="font-size:.8rem;">
                                            {{ $item->stokByLocation($item->master_location) }} {{ $item->unit }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada barang yang dapat dihapus.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-3 gap-2">
                    <button type="button" class="btn btn-light fw-semibold px-4" data-bs-dismiss="modal" style="border-radius:10px;font-size:.875rem;border:1px solid #e5e7eb;">Batal</button>
                    <button type="submit" class="btn text-white fw-semibold px-4 flex-grow-1" style="background:linear-gradient(135deg,#ef4444,#b91c1c);border:none;border-radius:10px;font-size:.875rem;" onclick="return confirm('Apakah Anda yakin ingin menghapus barang terpilih secara permanen beserta riwayatnya?')">Hapus Barang Terpilih</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAllHapus');
        const checkItems = document.querySelectorAll('.check-item-hapus');

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkItems.forEach(item => {
                    item.checked = this.checked;
                });
            });

            checkItems.forEach(item => {
                item.addEventListener('change', function () {
                    const allChecked = Array.from(checkItems).every(i => i.checked);
                    const someChecked = Array.from(checkItems).some(i => i.checked);
                    checkAll.checked = allChecked;
                    checkAll.indeterminate = someChecked && !allChecked;
                });
            });
        }
    });
</script>
@endpush

@endsection
