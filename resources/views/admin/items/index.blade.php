@extends('layouts.app')
@section('title', 'Master Barang')
@section('content')
@php
$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
@endphp
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header flex-wrap gap-2">
    <h2 class="h5 mb-0">Master Barang</h2>
    <div class="d-flex align-items-center gap-2">
        <button type="button" id="btnKirimBarang" class="btn btn-sm text-white text-nowrap" style="background:var(--kk-accent); display:none;"
                data-bs-toggle="modal" data-bs-target="#modalKirimBarang">
            <i class="bi bi-send"></i> Kirim Barang
        </button>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" id="masterBarangSearch" class="form-control" placeholder="Cari barang...">
        </div>
    </div>
</div>

<div class="kk-stat-card">
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#mb-gudang-utama">
                <i class="bi bi-building"></i> Gudang Utama
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#mb-gudang-resto">
                <i class="bi bi-shop"></i> Gudang Resto
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#mb-kasir">
                <i class="bi bi-cash-coin"></i> Kasir
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#mb-kitchen">
                <i class="bi bi-egg-fried"></i> Kitchen
            </a>
        </li>
    </ul>

    <div class="tab-content">
        @foreach($grouped as $key => $groupItems)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="mb-{{ str_replace('_', '-', $key) }}">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Hari, Tanggal &amp; Jam</th>
                            <th>Satuan</th>
                            <th>Keterangan</th>
                            <th class="text-end">Stock</th>
                            <th style="width:1%;white-space:nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupItems as $item)
                        @php $activity = $item->latestActivity(); @endphp
                        <tr class="kk-mb-row" data-name="{{ strtolower($item->name) }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td>
                                @if($activity)
                                    {{ $hariIndo[$activity->tanggal->format('l')] }}, {{ $activity->tanggal->format('d-m-Y') }} {{ $activity->created_at->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $activity->keterangan ?? '-' }}</td>
                            <td class="text-end">
                                <span class="badge {{ $item->totalStock() <= $item->min_stock ? 'bg-danger' : 'bg-primary' }}">
                                    {{ $item->totalStock() }}
                                </span>
                            </td>
                            <td style="width:1%;white-space:nowrap;">
                                <form action="{{ route('admin.items.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Hapus barang ini? Riwayat stok terkait juga ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Belum ada barang di bagian ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Modal: Kirim Barang dari Gudang Utama --}}
<div class="modal fade" id="modalKirimBarang" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.items.send') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Barang dari Gudang Utama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                        <select name="item_id" id="kirimItemSelect" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">— Pilih Barang —</option>
                            @foreach($grouped['gudang_utama'] as $item)
                            <option value="{{ $item->id }}" data-stok="{{ $item->stokGudang() }}" data-unit="{{ $item->unit }}">
                                {{ $item->name }} (Stok: {{ $item->stokGudang() }} {{ $item->unit }})
                            </option>
                            @endforeach
                        </select>
                        @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kirim ke <span class="text-danger">*</span></label>
                        <select name="destination" class="form-select @error('destination') is-invalid @enderror" required>
                            <option value="">— Pilih Tujuan —</option>
                            <option value="gudang_resto">Gudang Resto</option>
                            <option value="kasir">Kasir</option>
                            <option value="kitchen">Kitchen</option>
                        </select>
                        @error('destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" id="keteranganInput" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Pilih catatan lama atau ketik catatan baru (opsional)" value="{{ old('keterangan') }}" autocomplete="off">
                        <div id="keteranganDropdown" class="list-group position-absolute w-100 shadow-sm" style="z-index:1060; max-height:220px; overflow-y:auto; display:none; top:100%;"></div>
                        @error('keterangan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-2 mb-1">
                        <div class="col-6">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="kirimQuantity" class="form-control @error('quantity') is-invalid @enderror" min="1" value="1" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="background:var(--kk-accent)">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('masterBarangSearch');

        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.kk-mb-row').forEach(function (row) {
                row.style.display = row.dataset.name.includes(term) ? '' : 'none';
            });
        });

        // Tombol "Kirim Barang" cuma tampil kalau tab "Gudang Utama" yang aktif.
        const btnKirim = document.getElementById('btnKirimBarang');
        const gudangUtamaPane = document.getElementById('mb-gudang-utama');

        function toggleKirimButton() {
            btnKirim.style.display = gudangUtamaPane.classList.contains('active') ? 'inline-block' : 'none';
        }

        toggleKirimButton();

        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tabEl) {
            tabEl.addEventListener('shown.bs.tab', toggleKirimButton);
        });

        // Batasi jumlah maksimal sesuai stok Gudang Utama barang yang dipilih.
        const kirimItemSelect = document.getElementById('kirimItemSelect');
        const kirimQuantity = document.getElementById('kirimQuantity');

        kirimItemSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                kirimQuantity.max = opt.getAttribute('data-stok');
            }
        });

        // Dropdown saran Keterangan: pilih catatan lama, ketik manual, atau
        // hapus catatan lama lewat tombol "x".
        let catatanList = @json($keteranganSuggestions);
        const keteranganInput = document.getElementById('keteranganInput');
        const keteranganDropdown = document.getElementById('keteranganDropdown');

        function renderKeteranganDropdown(filterText) {
            const term = (filterText || '').toLowerCase();
            const items = catatanList.filter(function (c) {
                return c.teks.toLowerCase().includes(term);
            });

            if (items.length === 0) {
                keteranganDropdown.style.display = 'none';
                keteranganDropdown.innerHTML = '';
                return;
            }

            keteranganDropdown.innerHTML = items.map(function (c) {
                return '<div class="list-group-item d-flex justify-content-between align-items-center py-2" style="cursor:pointer;">' +
                    '<span class="kk-catatan-pick flex-grow-1" data-teks="' + c.teks.replace(/"/g, '&quot;') + '">' + c.teks + '</span>' +
                    '<button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2 kk-catatan-hapus" data-id="' + c.id + '" title="Hapus catatan ini dari daftar saran">' +
                    '<i class="bi bi-x-lg"></i></button>' +
                    '</div>';
            }).join('');
            keteranganDropdown.style.display = 'block';
        }

        keteranganInput.addEventListener('focus', function () {
            renderKeteranganDropdown(this.value);
        });
        keteranganInput.addEventListener('input', function () {
            renderKeteranganDropdown(this.value);
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.kk-catatan-pick')) {
                const span = e.target.closest('.kk-catatan-pick');
                keteranganInput.value = span.getAttribute('data-teks');
                keteranganDropdown.style.display = 'none';
                return;
            }

            const tombolHapus = e.target.closest('.kk-catatan-hapus');
            if (tombolHapus) {
                e.preventDefault();
                e.stopPropagation();

                if (!confirm('Hapus catatan ini dari daftar saran? Riwayat pengiriman yang sudah tercatat tidak akan berubah.')) {
                    return;
                }

                const id = tombolHapus.getAttribute('data-id');

                fetch('{{ url("admin/items/keterangan") }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                }).then(function (res) {
                    if (res.ok) {
                        catatanList = catatanList.filter(function (c) { return String(c.id) !== String(id); });
                        renderKeteranganDropdown(keteranganInput.value);
                    }
                });
                return;
            }

            if (!keteranganDropdown.contains(e.target) && e.target !== keteranganInput) {
                keteranganDropdown.style.display = 'none';
            }
        });
    });
</script>
@endsection
