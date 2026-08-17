@extends('layouts.app')
@section('title', 'Total Stock Barang')
@section('content')
<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2 class="h5 mb-0">Total Stock Barang</h2>
    <div class="d-flex align-items-center gap-2 flex-nowrap">
        <div class="kk-search-box">
            <i class="bi bi-search"></i>
            <form action="{{ route('kasir.stock_harian.index') }}" method="GET" class="m-0">
                <input type="text" name="kk_search" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang..." value="{{ request('kk_search') }}" autocomplete="off">
            </form>
        </div>
    </div>
</div>

<div class="kk-stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 text-center align-middle table-sm">
            <thead class="table-light">
                <tr>
                    <th class="text-center align-middle" style="width: 4%;">No</th>
                    <th class="text-center align-middle" style="width: 16%;">Hari, Jam, &amp; Tanggal</th>
                    <th class="text-center align-middle" style="width: 14%;">Nama Barang</th>
                    <th class="text-center align-middle" style="color:#059669; width: 6%;">Masuk</th>
                    <th class="text-center align-middle" style="color:#dc2626; width: 6%;">Keluar</th>
                    <th class="text-center align-middle" style="color:#0284c7; width: 6%;">Sisa</th>
                    <th class="text-center align-middle" style="width: 8%;">Satuan</th>
                    <th class="text-center align-middle" style="width: 12%;">Devisi</th>
                    <th class="text-center align-middle" style="width: 18%;">Keterangan</th>
                    <th class="text-center align-middle" style="width: 10%;">Dicatat Oleh</th>
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
    <div class="p-3">{{ $items->appends(request()->query())->links() }}</div>
    @endif
</div>
</div>

{{-- Modal Lapor Stock Total (Kasir) --}}
<div class="modal fade" id="modalLaporStok" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <form action="{{ route('kasir.stock.adjust') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);padding:1.4rem 1.5rem 2.5rem;">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold text-white" style="font-size:1.05rem;">Lapor Stock Total</h5>
                        <small style="color:rgba(255,255,255,.75);font-size:.78rem;">Laporan stok manual secara massal untuk Kasir</small>
                    </div>
                </div>
                <div class="modal-body px-4" style="margin-top:-1.2rem;padding-top:0;">
                    <div class="d-flex align-items-center gap-2 p-3 mb-3 rounded-3" style="background:#e0f2fe;border:1px solid #bae6fd;">
                        <i class="bi bi-info-circle-fill" style="color:#0284c7;font-size:1rem;flex-shrink:0;"></i>
                        <small style="color:#0369a1;line-height:1.4;">
                            Masukkan jumlah stok yang sebenarnya. Sistem akan mencatat penyesuaian ini sebagai laporan stok.
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
                                    <th class="text-center" style="font-size:.85rem;color:#374151;white-space:nowrap;min-width:120px;">Sisa</th>
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
                    <button type="submit" class="btn text-white fw-semibold px-4 flex-grow-1" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);border:none;border-radius:10px;font-size:.875rem;">Simpan Laporan</button>
                </div>
            </form>
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
