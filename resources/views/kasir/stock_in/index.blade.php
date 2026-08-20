@extends('layouts.app')
@section('title', 'Stock Barang Masuk')
@section('content')
<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2 class="h5 mb-0">Stock Barang Masuk</h2>
    <div class="d-flex align-items-center gap-2 flex-nowrap">
        <form action="{{ route('kasir.stock_in.index') }}" method="GET" class="m-0">
            <input type="hidden" name="kk_search" value="{{ request('kk_search') }}">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-dark"><i class="bi bi-calendar-date"></i></span>
                <input type="date" name="tanggal" class="form-control border-start-0 ps-0" value="{{ request('tanggal', now()->toDateString()) }}" onchange="this.form.submit()" style="max-width: 130px;" title="Filter berdasarkan tanggal">
            </div>
        </form>
        <button type="button" class="btn btn-sm text-white text-nowrap" style="background:#0ea5e9;" data-bs-toggle="modal" data-bs-target="#modalAturStok">
            <i class="bi bi-sliders"></i> Atur Jumlah Stock
        </button>
        <div class="kk-search-box">
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
                    <th class="text-center">Hari, Jam &amp; Tanggal</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Dicatat Oleh</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockIns as $s)
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center">{{ $s->created_at->translatedFormat('l, H:i, d-m-Y') }}</td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $s->item->name }}</td>
                    <td class="text-center">{{ $s->quantity }}</td>
                    <td class="text-center">{{ $s->item->kasir_unit ?? $s->item->unit }}</td>
                    <td class="text-center">{{ $s->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        @php $roleName = $s->user->role?->name ?? '?'; @endphp
                        @if($roleName === 'Admin')
                            <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $roleName }}</span>
                        @elseif(in_array($roleName, ['Kasir', 'Kitchen']))
                            <span class="badge" style="background:#bae6fd;color:#0369a1;font-weight:600;">{{ $roleName }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $roleName }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form action="{{ route('kasir.stock_in.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat barang masuk ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light text-danger p-1" style="border:1px solid #fee2e2;background:#fef2f2;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Belum ada catatan masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stockIns->hasPages())
    <div class="p-3">{{ $stockIns->links() }}</div>
    @endif
</div>
</div>

{{-- Modal Atur Jumlah Stok (Kasir) --}}
<div class="modal fade" id="modalAturStok" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:650px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <form action="{{ route('kasir.stock.adjust') }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal" value="{{ request('tanggal', now()->toDateString()) }}">
                <div class="modal-header border-0 pb-3" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);padding:1rem 1.25rem;">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold text-white" style="font-size:.95rem;">Atur Jumlah Stock</h5>
                    </div>
                </div>
                <div class="modal-body px-3 pt-3">
                    <style>
                        .kk-unit-ro { display:flex;align-items:center;padding:0 6px;font-weight:700;font-size:.75rem;white-space:nowrap;color:#374151; }
                        .modal-adj th, .modal-adj td { border:1px solid #d1d5db !important; padding:4px 6px !important; }
                        .modal-adj tr:first-child th { border-top:none !important; }
                        .modal-adj tr:last-child  td { border-bottom:none !important; }
                        .modal-adj tr th:first-child, .modal-adj tr td:first-child { border-left:none !important; }
                        .modal-adj tr th:last-child,  .modal-adj tr td:last-child  { border-right:none !important; }
                        .modal-adj input[type=number] { font-weight:700;font-size:.82rem;border:none;box-shadow:none;padding:2px 4px; }
                        .modal-adj input[type=text]   { font-weight:600;font-size:.75rem;border:none;box-shadow:none;padding:2px 4px; }
                        .kk-ig { border-radius:6px;overflow:hidden; }
                    </style>
                    <div class="table-responsive" style="max-height:360px;overflow-y:auto;border:1px solid #d1d5db;border-radius:8px;margin-top:.75rem;">
                        <table class="table table-hover align-middle mb-0 modal-adj" style="border-collapse:collapse;font-size:.82rem;">
                            <thead class="table-light sticky-top" style="z-index:1;">
                                <tr>
                                    <th class="text-center" style="width:32px;">No</th>
                                    <th class="text-center" style="width:24%;">Nama Barang</th>
                                    <th class="text-center" style="min-width:110px;">Masuk</th>
                                    <th class="text-center" style="min-width:125px;">Keluar</th>
                                    <th class="text-center" style="min-width:125px;">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($allItemsForAdjust))
                                @forelse($allItemsForAdjust as $item)
                                @php
                                    $currentMasuk  = $item->masukByLocation($item->master_location);
                                    $currentKeluar = $item->keluarByLocation($item->master_location);
                                    $currentSisa   = $item->stokByLocation($item->master_location);
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="text-center fw-semibold">{{ $item->name }}</td>

                                    {{-- Masuk --}}
                                    <td>
                                        <div class="input-group input-group-sm mx-auto flex-nowrap kk-ig" style="max-width:108px;box-shadow:0 0 0 1.5px #bbf7d0;">
                                            <input type="number" step="any" name="new_masuk[{{ $item->id }}]"
                                                   class="form-control text-center new-masuk-input"
                                                   min="0" value="{{ $currentMasuk }}"
                                                   data-item-id="{{ $item->id }}"
                                                   style="color:#059669;background-color:#f0fdf4;" readonly>
                                            <span class="kk-unit-ro" style="background:#f0fdf4;border-left:1.5px solid #bbf7d0;">{{ $item->unit }}</span>
                                        </div>
                                    </td>

                                    {{-- Keluar --}}
                                    <td>
                                        <div class="input-group input-group-sm mx-auto flex-nowrap kk-ig" style="max-width:125px;box-shadow:0 0 0 1.5px #fecaca;">
                                            <input type="number" step="any" name="new_keluar[{{ $item->id }}]"
                                                   class="form-control text-center new-keluar-input"
                                                   min="0" value="{{ $currentKeluar }}"
                                                   data-item-id="{{ $item->id }}"
                                                   style="color:#dc2626;max-width:50px;">
                                            <input type="text" name="new_keluar_unit[{{ $item->id }}]"
                                                   class="form-control text-center"
                                                   value="{{ $item->kasir_unit ?? $item->unit }}" maxlength="30" placeholder="Sat."
                                                   style="color:#9f1239;border-left:1.5px solid #fecaca;background:#fff1f2;">
                                        </div>
                                    </td>

                                    {{-- Stock & Satuan --}}
                                    <td>
                                        <div class="input-group input-group-sm mx-auto kk-ig" style="max-width:125px;box-shadow:0 0 0 1.5px #bae6fd;">
                                            <input type="number" step="any" name="new_stock[{{ $item->id }}]"
                                                    class="form-control text-center new-stock-input"
                                                    min="0" value="{{ $currentSisa }}"
                                                    data-item-id="{{ $item->id }}"
                                                    style="color:#0284c7;max-width:50px;">
                                            <input type="text" name="new_unit[{{ $item->id }}]"
                                                    class="form-control text-center new-unit-input"
                                                    value="{{ $item->kasir_unit ?? $item->unit }}" maxlength="30" placeholder="Sat."
                                                    data-item-id="{{ $item->id }}"
                                                    style="color:#065f46;border-left:1.5px solid #bae6fd;">
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada barang.</td></tr>
                                @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 px-3 pb-3 pt-2 gap-2">
                    <button type="button" class="btn btn-sm btn-light fw-semibold px-3" data-bs-dismiss="modal" style="border-radius:8px;border:1px solid #e5e7eb;">Batal</button>
                    <button type="submit" class="btn btn-sm text-white fw-semibold px-3 flex-grow-1" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);border:none;border-radius:8px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var masukInputs  = document.querySelectorAll('.new-masuk-input');
        var keluarInputs = document.querySelectorAll('.new-keluar-input');
        var stockInputs  = document.querySelectorAll('.new-stock-input');

        // Sinkronisasi otomatis dihapus atas permintaan agar bisa diisi sendiri-sendiri
    });
</script>
@endpush