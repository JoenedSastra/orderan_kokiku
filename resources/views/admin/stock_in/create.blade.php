@extends('layouts.app')
@section('title', 'Catatan Barang Masuk Di ' . $lokasiLabel)
@section('content')

<div class="mb-3 kk-page-header d-flex align-items-center gap-3 flex-wrap">
    <a href="{{ route('admin.stock_in.index') }}?dari=kembali" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 flex-shrink-0"
       style="background:var(--kk-danger);">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    @php
        $divisiInfo = [
            'gudang_utama' => [
                'icon'  => 'bi-building',
                'color' => '#2563eb',
                'bg'    => 'rgba(37,99,235,0.10)',
                'label' => 'Gudang Utama',
                'desc'  => 'Pencatatan barang masuk untuk stok utama gudang pusat',
            ],
            'gudang_resto' => [
                'icon'  => 'bi-shop',
                'color' => '#16a34a',
                'bg'    => 'rgba(22,163,74,0.10)',
                'label' => 'Gudang Resto',
                'desc'  => 'Pencatatan barang masuk untuk kebutuhan restoran',
            ],
            'kasir' => [
                'icon'  => 'bi-cash-coin',
                'color' => '#dc2626',
                'bg'    => 'rgba(220,38,38,0.10)',
                'label' => 'Kasir',
                'desc'  => 'Pencatatan barang masuk untuk kebutuhan operasional kasir',
            ],
            'kitchen' => [
                'icon'  => 'bi-fire',
                'color' => '#d97706',
                'bg'    => 'rgba(217,119,6,0.10)',
                'label' => 'Kitchen',
                'desc'  => 'Pencatatan barang masuk untuk kebutuhan dapur & memasak',
            ],
        ];
        $info = $divisiInfo[$lokasi] ?? ['icon' => 'bi-box', 'color' => '#6b7280', 'bg' => 'rgba(107,114,128,0.10)', 'label' => $lokasiLabel, 'desc' => 'Pencatatan barang masuk'];
    @endphp

    <div class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
             style="width:36px; height:36px; background:{{ $info['bg'] }};">
            <i class="bi {{ $info['icon'] }}" style="color:{{ $info['color'] }}; font-size:1rem;"></i>
        </div>
        <div style="line-height:1.25;">
            <div class="fw-bold" style="font-size:0.9rem; color:var(--kk-text);">
                Catatan Barang Masuk {{ $info['label'] }}
            </div>
            <div style="font-size:0.75rem; color:var(--kk-text-muted);">
                {{ $info['desc'] }}
            </div>
        </div>
    </div>
    
    <div class="ms-auto d-flex align-items-center gap-2">
        <a href="{{ route('admin.stock_in.riwayat') }}?lokasi={{ $lokasi }}" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" title="Riwayat Barang Masuk">
            <i class="bi bi-clock-history"></i> Riwayat Barang Masuk
        </a>
        <div class="kk-search-box" style="width:250px;">
            <i class="bi bi-search"></i>
            <input type="text" id="inputCariBarang" class="form-control form-control-sm" placeholder="Cari nama barang di sini...">
        </div>
    </div>
</div>

@if($errors->has('rows'))
<div class="alert alert-danger d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('rows') }}
</div>
@endif

<form action="{{ route('admin.stock_in.store', ['lokasi' => $lokasi]) }}" method="POST" id="formBarangMasukMassal">
    @csrf

    <style>
        /* Hilangkan border bawaan dari cell pinggir agar menempel rata dengan navy card */
        #tabelBarangMasuk { border: none !important; }
        #tabelBarangMasuk thead th { border-top: none !important; }
        #tabelBarangMasuk th:first-child, #tabelBarangMasuk td:first-child { border-left: none !important; }
        #tabelBarangMasuk th:last-child, #tabelBarangMasuk td:last-child { border-right: none !important; }
        #tabelBarangMasuk tbody tr:last-child td { border-bottom: none !important; }
        .row-number::before {
            counter-increment: rowNumber;
            content: counter(rowNumber);
        }
    </style>
    <div class="kk-stat-card mb-4 rounded-0" style="background-color: #dc2626; padding: 2px; border: none;">
        <div class="table-responsive" style="max-height:70vh; background-color: var(--kk-surface);">
            <table class="table table-bordered table-sm mb-0 align-middle" id="tabelBarangMasuk">
                <thead class="table-light" style="position:sticky; top:0; z-index:1;">
                        <th class="text-center" style="width:40px;">No</th>
                        <th class="text-center" style="width:220px;">Nama Barang</th>
                        <th class="text-center" style="width:90px;">Jumlah</th>
                        <th class="text-center" style="width:110px;">Satuan</th>
                        <th class="text-center" style="width:200px;">Keterangan</th>
                        <th class="text-center" style="width:130px;">Devisi</th>
                        <th class="text-center" style="width:60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabelBarangMasukBody" style="counter-reset: rowNumber;">
                    @php $maxIndex = -1; @endphp
                    @forelse($existingItems as $index => $item)
                    @php $maxIndex = $index; @endphp
                    <tr data-item-id="{{ $item->id }}">
                        <td class="text-center text-muted fw-bold row-number"></td>
                        <td>
                            <input type="text" name="rows[{{ $index }}][item_name]"
                                   value="{{ $item->name }}" readonly autocomplete="off"
                                   class="form-control form-control-sm kk-baris-nama fw-bold" style="background-color: #f9fafb;">
                        </td>
                        <td>
                            <input type="number" step="any" name="rows[{{ $index }}][quantity]" min="0"
                                   class="form-control form-control-sm text-center">
                        </td>
                        <td>
                            <input type="text" name="rows[{{ $index }}][unit]"
                                   value=""
                                   class="form-control form-control-sm text-center">
                        </td>
                        <td>
                            <input type="text" name="rows[{{ $index }}][keterangan]" autocomplete="off"
                                   class="form-control form-control-sm">
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:#bfdbfe;color:#1d4ed8;font-weight:600;">{{ $lokasiLabel }}</span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris" style="border:none;" title="Hapus baris ini">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <!-- Kosong, menunggu ditambah baris -->
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-2 border-top text-center" style="background: var(--kk-surface);">
                <button type="button" id="btnTambahBaris" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">
                    <i class="bi bi-plus-lg"></i> Tambah Baris Baru
                </button>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <span class="small text-muted"><span id="kkBarisTerisi">0</span> baris terisi</span>
        <div class="d-flex gap-2">
            <button type="button" id="btnKosongkan" class="btn btn-danger btn-sm">
                <i class="bi bi-eraser"></i> Kosongkan Semua
            </button>
            <button type="submit" class="btn text-white" style="background:#2563eb;">
                <i class="bi bi-send-fill"></i> Kirim ke {{ $lokasiLabel }}
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
(function () {
    const form   = document.getElementById('formBarangMasukMassal');
    const tabel  = document.getElementById('tabelBarangMasuk');
    const hitung = document.getElementById('kkBarisTerisi');
    const btnKosongkan = document.getElementById('btnKosongkan');

    // Ingat lokasi ini sebagai yang terakhir dibuka, supaya halaman "pilih divisi"
    // bisa langsung lompat ke sini di kunjungan berikutnya.
    localStorage.setItem('kk_last_lokasi', '{{ $lokasi }}');
    const draftKey = 'draft_rows_{{ $lokasi }}';
    const lokasiLabel = '{{ $lokasiLabel }}';

    function updateHitung() {
        const qtyInputs = tabel.querySelectorAll('input[type="number"]');
        let terisi = 0;
        qtyInputs.forEach(function (input) {
            if (input.value.trim() !== '') terisi++;
        });
        hitung.textContent = terisi;
    }

    function saveDraft() {
        const rowsData = [];
        tabel.querySelectorAll('tbody tr').forEach(function(tr) {
            const inputNama = tr.querySelector('.kk-baris-nama');
            const inputQty = tr.querySelector('input[type="number"]');
            const inputUnit = tr.querySelector('input[name*="[unit]"]');
            const inputKet = tr.querySelector('input[name*="[keterangan]"]');
            
            if (inputNama) {
                rowsData.push({
                    item_id: tr.dataset.itemId || null,
                    item_name: inputNama.value,
                    quantity: inputQty ? inputQty.value : '',
                    unit: inputUnit ? inputUnit.value : '',
                    keterangan: inputKet ? inputKet.value : '',
                    is_readonly: inputNama.hasAttribute('readonly')
                });
            }
        });
        localStorage.setItem(draftKey, JSON.stringify(rowsData));
    }

    const tbody = document.getElementById('tabelBarangMasukBody');

    function loadDraft() {
        const draftStr = localStorage.getItem(draftKey);
        if (draftStr) {
            try {
                const rowsData = JSON.parse(draftStr);
                tbody.innerHTML = ''; // Kosongkan bawaan database, gunakan draft
                rowsData.forEach(row => {
                    tambahBaris(row.item_name, row.quantity, row.unit, row.keterangan, row.is_readonly, row.item_id);
                });
                updateHitung();
            } catch(e) {
                console.error("Gagal memuat draft", e);
            }
        }
    }

    tabel.addEventListener('input', function (e) {
        updateHitung();
        saveDraft();
    });

    btnKosongkan.addEventListener('click', function () {
        if (!confirm('Yakin ingin mengosongkan semua isian jumlah?')) return;
        tabel.querySelectorAll('input:not([readonly])').forEach(function (input) {
            input.value = '';
        });
        updateHitung();
        saveDraft();
    });

    // --- Filter Pencarian Lokal ---
    const inputCari = document.getElementById('inputCariBarang');
    if (inputCari) {
        inputCari.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const inputNama = row.querySelector('.kk-baris-nama');
                if (inputNama) {
                    const text = inputNama.value.toLowerCase();
                    if (text.indexOf(filter) > -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }

    tabel.addEventListener('click', function(e) {
        const btnHapus = e.target.closest('.btn-hapus-baris');
        if (btnHapus) {
            const tr = btnHapus.closest('tr');
            const itemId = tr.dataset.itemId;

            if (itemId && itemId !== 'null' && itemId !== 'undefined') {
                if (!confirm('Yakin ingin menghapus barang ini secara permanen dari daftar master dan menghapus riwayat stoknya di divisi ini?')) return;
                
                fetch(`{{ url('/admin/stock-masuk/item') }}/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        tr.remove();
                        updateHitung();
                        saveDraft();
                    } else {
                        alert('Gagal menghapus barang dari sistem.');
                    }
                }).catch(err => alert('Terjadi kesalahan koneksi.'));
            } else {
                tr.remove();
                updateHitung();
                saveDraft();
            }
        }
    });

    form.addEventListener('submit', function() {
        // Hapus draft jika form disubmit
        localStorage.removeItem(draftKey);
    });

    const btnTambahBaris = document.getElementById('btnTambahBaris');

    function tambahBaris(nama = '', qty = '', unit = '', ket = '', isReadonly = false, itemId = null) {
        const tr = document.createElement('tr');
        const uniqueId = Date.now() + Math.floor(Math.random() * 1000);
        
        const readonlyAttr = isReadonly ? 'readonly' : '';
        const bgStyle = isReadonly ? 'style="background-color: #f9fafb;"' : '';

        if (itemId) {
            tr.dataset.itemId = itemId;
        }

        tr.innerHTML = `
            <td class="text-center text-muted fw-bold row-number"></td>
            <td>
                <input type="text" name="rows[${uniqueId}][item_name]" value="${nama}"
                       class="form-control form-control-sm kk-baris-nama fw-bold" autocomplete="off" ${readonlyAttr} ${bgStyle}>
            </td>
            <td>
                <input type="number" step="any" name="rows[${uniqueId}][quantity]" value="${qty}" min="0"
                       class="form-control form-control-sm text-center">
            </td>
            <td>
                <input type="text" name="rows[${uniqueId}][unit]" value="${unit}"
                       autocomplete="off"
                       class="form-control form-control-sm text-center">
            </td>
            <td>
                <input type="text" name="rows[${uniqueId}][keterangan]" value="${ket}" autocomplete="off"
                       class="form-control form-control-sm">
            </td>
            <td class="text-center">
                <span class="badge" style="background:#bfdbfe;color:#1d4ed8;font-weight:600;">${lokasiLabel}</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris" style="border:none;" title="Hapus baris ini">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        return tr;
    }

    btnTambahBaris.addEventListener('click', function () {
        const tr = tambahBaris();
        tr.querySelector('.kk-baris-nama').focus();
        saveDraft();
    });

    // Jalankan loadDraft saat awal
    loadDraft();
    updateHitung();



    // ── Navigasi Excel-like ────────────────────────────────────────────────
    // Bangun grid dua dimensi: rows × cols (hanya <input> yang bisa difokus).
    function buildGrid() {
        const grid = [];
        tabel.querySelectorAll('tbody tr').forEach(function (tr) {
            const inputs = Array.from(tr.querySelectorAll('input'));
            if (inputs.length) grid.push(inputs);
        });
        return grid;
    }

    function findCell(grid, input) {
        for (let r = 0; r < grid.length; r++) {
            const c = grid[r].indexOf(input);
            if (c !== -1) return { r, c };
        }
        return null;
    }

    function focusCell(grid, r, c) {
        const row = grid[r];
        if (!row) return;
        const col = Math.max(0, Math.min(c, row.length - 1));
        const el = row[col];
        if (el) {
            el.focus();
            // Tempatkan kursor di akhir teks agar tidak overwrite
            const len = el.value.length;
            try { el.setSelectionRange(len, len); } catch (_) {}
        }
    }

    tabel.addEventListener('keydown', function (e) {
        const input = e.target;
        if (input.tagName !== 'INPUT') return;

        const grid = buildGrid();
        const pos  = findCell(grid, input);
        if (!pos) return;

        const { r, c } = pos;

        // type="number" tidak mendukung selectionStart/End (selalu null)
        // → pada number input, selalu anggap bisa pindah ke kiri/kanan
        const isNumber = input.type === 'number';
        const atStart  = isNumber ? true  : (input.selectionStart === 0 && input.selectionEnd === 0);
        const atEnd    = isNumber ? true  : (input.selectionStart === input.value.length && input.selectionEnd === input.value.length);

        switch (e.key) {
            case 'ArrowDown':
                // Cegah browser mengubah nilai angka
                e.preventDefault();
                focusCell(grid, r + 1, c);
                break;

            case 'Enter':
                e.preventDefault();
                focusCell(grid, r + 1, c);
                break;

            case 'ArrowUp':
                // Cegah browser mengubah nilai angka
                e.preventDefault();
                focusCell(grid, r - 1, c);
                break;

            case 'ArrowRight':
                if (atEnd) {
                    e.preventDefault();
                    if (c + 1 < grid[r].length) {
                        focusCell(grid, r, c + 1);
                    } else if (r + 1 < grid.length) {
                        focusCell(grid, r + 1, 0);
                    }
                }
                break;

            case 'ArrowLeft':
                if (atStart) {
                    e.preventDefault();
                    if (c - 1 >= 0) {
                        focusCell(grid, r, c - 1);
                    } else if (r - 1 >= 0) {
                        focusCell(grid, r - 1, grid[r - 1].length - 1);
                    }
                }
                break;

            case 'Tab':
                e.preventDefault();
                if (e.shiftKey) {
                    if (c - 1 >= 0) {
                        focusCell(grid, r, c - 1);
                    } else if (r - 1 >= 0) {
                        focusCell(grid, r - 1, grid[r - 1].length - 1);
                    }
                } else {
                    if (c + 1 < grid[r].length) {
                        focusCell(grid, r, c + 1);
                    } else if (r + 1 < grid.length) {
                        focusCell(grid, r + 1, 0);
                    }
                }
                break;
        }
    });

    btnKosongkan.addEventListener('click', function () {
        if (!confirm('Kosongkan semua isian baris?')) return;
        tabel.querySelectorAll('input').forEach(function (input) { input.value = ''; });
        updateHitung();
        clearDraft();
    });

    form.addEventListener('submit', function (e) {
        if (parseInt(hitung.textContent, 10) === 0) {
            e.preventDefault();
            alert('Isi minimal 1 baris sebelum kirim.');
            return;
        }
        // Data berhasil dikirim ke server, draft lokal tidak diperlukan lagi.
        clearDraft();
    });
})();
</script>
@endpush
@endsection
