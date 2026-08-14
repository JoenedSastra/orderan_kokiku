@extends('layouts.app')
@section('title', 'Catatan Barang Masuk Di ' . $lokasiLabel)
@section('content')

<div class="mb-3 kk-page-header">
    <a href="{{ route('admin.stock_in.index') }}?dari=kembali" class="btn btn-sm text-white d-inline-flex align-items-center gap-1"
       style="background:var(--kk-danger);">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if($errors->has('rows'))
<div class="alert alert-danger d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('rows') }}
</div>
@endif

<form action="{{ route('admin.stock_in.store', ['lokasi' => $lokasi]) }}" method="POST" id="formBarangMasukMassal">
    @csrf

    <div class="kk-stat-card p-0">
        <div class="table-responsive" style="max-height:70vh;">
            <table class="table table-bordered table-sm mb-0 align-middle" id="tabelBarangMasuk">
                <thead class="table-light" style="position:sticky; top:0; z-index:1;">
                    <tr>
                        <th class="text-center" style="width:40px;">No</th>
                        <th class="text-center" style="width:220px;">Nama Barang</th>
                        <th class="text-center" style="width:90px;">Jumlah</th>
                        <th class="text-center" style="width:110px;">Satuan</th>
                        <th class="text-center" style="width:110px;">Master</th>
                        <th class="text-center" style="width:200px;">Keterangan</th>
                        <th class="text-center" style="width:130px;">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $jumlahBaris; $i++)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                        <td>
                            <input type="text" name="rows[{{ $i }}][item_name]"
                                   autocomplete="off"
                                   class="form-control form-control-sm kk-baris-nama">
                        </td>
                        <td>
                            <input type="number" name="rows[{{ $i }}][quantity]" min="1"
                                   class="form-control form-control-sm text-center">
                        </td>
                        <td>
                            <input type="text" name="rows[{{ $i }}][unit]"
                                   autocomplete="off"
                                   class="form-control form-control-sm text-center">
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $lokasiLabel }}</span>
                        </td>
                        <td>
                            <input type="text" name="rows[{{ $i }}][keterangan]" autocomplete="off"
                                   class="form-control form-control-sm">
                        </td>
                        <td class="text-center text-muted small">{{ auth()->user()->name }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <span class="small text-muted"><span id="kkBarisTerisi">0</span> dari {{ $jumlahBaris }} baris terisi</span>
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

    // Key unik per lokasi, supaya draft Gudang Utama & Gudang Resto tidak tercampur.
    const draftKey = 'kk_stock_draft_{{ $lokasi }}';
    const fieldKeys = ['item_name', 'quantity', 'unit', 'keterangan'];

    function saveDraft() {
        const rows = tabel.querySelectorAll('tbody tr');
        const data = [];
        rows.forEach(function (row) {
            const rowData = {};
            fieldKeys.forEach(function (key) {
                const input = row.querySelector('[name$="[' + key + ']"]');
                rowData[key] = input ? input.value : '';
            });
            data.push(rowData);
        });
        localStorage.setItem(draftKey, JSON.stringify(data));
    }

    function loadDraft() {
        const saved = localStorage.getItem(draftKey);
        if (!saved) return;
        try {
            const data = JSON.parse(saved);
            const rows = tabel.querySelectorAll('tbody tr');
            rows.forEach(function (row, i) {
                if (!data[i]) return;
                fieldKeys.forEach(function (key) {
                    const input = row.querySelector('[name$="[' + key + ']"]');
                    if (input && data[i][key]) input.value = data[i][key];
                });
            });
        } catch (e) {
            localStorage.removeItem(draftKey);
        }
    }

    function clearDraft() {
        localStorage.removeItem(draftKey);
    }

    function updateHitung() {
        const namaInputs = tabel.querySelectorAll('.kk-baris-nama');
        let terisi = 0;
        namaInputs.forEach(function (input) {
            if (input.value.trim() !== '') terisi++;
        });
        hitung.textContent = terisi;
    }

    // Pulihkan draft yang belum terkirim (kalau ada) saat halaman dibuka.
    loadDraft();
    updateHitung();

    tabel.addEventListener('input', function (e) {
        updateHitung();
        saveDraft();
    });

    tabel.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();

        const currentRow = e.target.closest('tr');
        const nextRow = currentRow ? currentRow.nextElementSibling : null;
        if (nextRow) {
            const nextInput = nextRow.querySelector('.kk-baris-nama');
            if (nextInput) nextInput.focus();
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
