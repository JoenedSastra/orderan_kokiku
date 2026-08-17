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
                    <td class="text-center">{{ $s->item->unit }}</td>
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
    <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <form action="{{ route('kasir.stock.adjust') }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal" value="{{ request('tanggal', now()->toDateString()) }}">
                <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);padding:1.4rem 1.5rem 2.5rem;">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold text-white" style="font-size:1.05rem;">Atur Jumlah Stock</h5>
                        <small style="color:rgba(255,255,255,.75);font-size:.78rem;">Penyesuaian stok manual secara massal untuk Kasir</small>
                    </div>
                </div>
                <div class="modal-body px-4" style="margin-top:-1.2rem;padding-top:0;">
                    <div class="d-flex align-items-center gap-2 p-3 mb-3 rounded-3" style="background:#e0f2fe;border:1px solid #bae6fd;">
                        <i class="bi bi-info-circle-fill" style="color:#0284c7;font-size:1rem;flex-shrink:0;"></i>
                        <small style="color:#0369a1;line-height:1.4;">
                            Masukkan jumlah stok yang sebenarnya. Sistem akan otomatis menyesuaikan jumlahnya.
                        </small>
                    </div>
                    
                    <div class="table-responsive border rounded-3" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;width:50px;">No</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;width:35%;white-space:nowrap;">Nama Barang</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;white-space:nowrap;min-width:100px;">Masuk</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;white-space:nowrap;min-width:100px;">Keluar</th>
                                    <th class="text-center" style="font-size:.85rem;color:#374151;white-space:nowrap;min-width:120px;">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($allItemsForAdjust))
                                @forelse($allItemsForAdjust as $item)
                                @php
                                    $currentMasuk = $item->masukByLocation($item->master_location);
                                    $currentKeluar = $item->keluarByLocation($item->master_location);
                                    $currentSisa = $item->stokByLocation($item->master_location);
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold align-middle" style="font-size:.875rem;">{{ $loop->iteration }}</td>
                                    <td class="text-center fw-bold align-middle" style="font-size:.875rem;">{{ $item->name }}</td>
                                    <td class="align-middle">
                                        <div class="input-group input-group-sm mx-auto flex-nowrap" style="max-width: 120px;">
                                            <input type="number" name="new_masuk[{{ $item->id }}]" class="form-control text-center new-masuk-input" min="0" value="{{ $currentMasuk }}" style="border-radius:6px; min-width: 60px; color:#059669; font-weight:600;" data-item-id="{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="input-group input-group-sm mx-auto flex-nowrap" style="max-width: 120px;">
                                            <input type="number" name="new_keluar[{{ $item->id }}]" class="form-control text-center new-keluar-input" min="0" value="{{ $currentKeluar }}" style="border-radius:6px; min-width: 60px; color:#dc2626; font-weight:600;" data-item-id="{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-light text-dark border px-3 py-2 fw-bold sisa-badge-{{ $item->id }}" style="font-size:.9rem; color:#0284c7 !important;">
                                            {{ $currentSisa }} {{ $item->unit }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada barang untuk disesuaikan.</td>
                                </tr>
                                @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-3 gap-2">
                    <button type="button" class="btn btn-light fw-semibold px-4" data-bs-dismiss="modal" style="border-radius:10px;font-size:.875rem;border:1px solid #e5e7eb;">Batal</button>
                    <button type="submit" class="btn text-white fw-semibold px-4 flex-grow-1" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);border:none;border-radius:10px;font-size:.875rem;">Simpan Perubahan</button>
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
        const masukInputs = document.querySelectorAll('.new-masuk-input');
        const keluarInputs = document.querySelectorAll('.new-keluar-input');

        function updateSisa(itemId) {
            const masukInput = document.querySelector(`.new-masuk-input[data-item-id="${itemId}"]`);
            const keluarInput = document.querySelector(`.new-keluar-input[data-item-id="${itemId}"]`);
            const sisaBadge = document.querySelector(`.sisa-badge-${itemId}`);
            
            if (masukInput && keluarInput && sisaBadge) {
                const masukVal = parseInt(masukInput.value) || 0;
                const keluarVal = parseInt(keluarInput.value) || 0;
                const sisa = Math.max(0, masukVal - keluarVal);
                
                // Get the unit from the existing text
                const unitMatch = sisaBadge.textContent.match(/[a-zA-Z]+/);
                const unit = unitMatch ? unitMatch[0] : '';
                
                sisaBadge.textContent = `${sisa} ${unit}`;
            }
        }

        masukInputs.forEach(input => {
            input.addEventListener('input', function() {
                updateSisa(this.getAttribute('data-item-id'));
            });
        });

        keluarInputs.forEach(input => {
            input.addEventListener('input', function() {
                updateSisa(this.getAttribute('data-item-id'));
            });
        });
    });
</script>
@endpush
