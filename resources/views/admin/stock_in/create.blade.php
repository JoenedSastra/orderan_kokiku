@extends('layouts.app')
@section('title', 'Catat Barang Masuk — ' . $lokasiLabel)
@section('content')

<div class="mb-3 kk-page-header">
    <a href="{{ route('admin.stock_in.index') }}" class="text-decoration-none small d-inline-flex align-items-center gap-1 mb-1" style="color:var(--kk-text-muted)">
        <i class="bi bi-arrow-left"></i> Kembali pilih divisi
    </a>
    <h2 class="h5 mb-1">Catat Barang Masuk — <span class="badge bg-secondary">{{ $lokasiLabel }}</span></h2>
    <p class="text-muted small mb-0">Isi manual tiap baris seperti Excel. Baris yang dibiarkan kosong otomatis diabaikan. Tanggal &amp; jam otomatis mengikuti waktu saat "Kirim" ditekan.</p>
</div>

@if($errors->has('rows'))
<div class="alert alert-danger d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('rows') }}
</div>
@endif

<datalist id="namaBarangOptions">
    @foreach($namaBarangSuggestions as $nama)
    <option value="{{ $nama }}">
    @endforeach
</datalist>

<datalist id="satuanOptions">
    @foreach($satuanSuggestions as $satuan)
    <option value="{{ $satuan }}">
    @endforeach
    <option value="Pcs"><option value="Buah"><option value="Botol"><option value="Kaleng">
    <option value="Pack"><option value="Bungkus"><option value="Karton"><option value="Box">
    <option value="Saset"><option value="Renteng"><option value="Jeriken">
    <option value="Gram"><option value="Kg"><option value="Ons">
    <option value="Ml"><option value="Liter">
</datalist>

<form action="{{ route('admin.stock_in.store', ['lokasi' => $lokasi]) }}" method="POST" id="formBarangMasukMassal">
    @csrf

    <div class="kk-stat-card p-0">
        <div class="table-responsive" style="max-height:70vh;">
            <table class="table table-bordered table-sm mb-0 align-middle" id="tabelBarangMasuk" style="min-width:920px;">
                <thead class="table-light" style="position:sticky; top:0; z-index:1;">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="min-width:200px;">Nama Barang</th>
                        <th style="width:100px;">Jumlah</th>
                        <th style="width:130px;">Satuan</th>
                        <th style="width:120px;">Master</th>
                        <th style="min-width:200px;">Keterangan</th>
                        <th style="width:140px;">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $jumlahBaris; $i++)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td>
                            <input type="text" name="rows[{{ $i }}][item_name]" list="namaBarangOptions"
                                   class="form-control form-control-sm kk-baris-nama" placeholder="Ketik nama barang">
                        </td>
                        <td>
                            <input type="number" name="rows[{{ $i }}][quantity]" min="1"
                                   class="form-control form-control-sm" placeholder="0">
                        </td>
                        <td>
                            <input type="text" name="rows[{{ $i }}][unit]" list="satuanOptions"
                                   class="form-control form-control-sm" placeholder="Kg, Pcs, ...">
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $lokasiLabel }}</span>
                        </td>
                        <td>
                            <input type="text" name="rows[{{ $i }}][keterangan]"
                                   class="form-control form-control-sm" placeholder="Diterima">
                        </td>
                        <td class="text-muted small">{{ auth()->user()->name }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <span class="small text-muted"><span id="kkBarisTerisi">0</span> dari {{ $jumlahBaris }} baris terisi</span>
        <div class="d-flex gap-2">
            <button type="button" id="btnKosongkan" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-eraser"></i> Kosongkan Semua
            </button>
            <button type="submit" class="btn text-white" style="background:var(--kk-accent)">
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

    function updateHitung() {
        const namaInputs = tabel.querySelectorAll('.kk-baris-nama');
        let terisi = 0;
        namaInputs.forEach(function (input) {
            if (input.value.trim() !== '') terisi++;
        });
        hitung.textContent = terisi;
    }

    tabel.addEventListener('input', updateHitung);
    updateHitung();

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
    });

    form.addEventListener('submit', function (e) {
        if (parseInt(hitung.textContent, 10) === 0) {
            e.preventDefault();
            alert('Isi minimal 1 baris sebelum kirim.');
        }
    });
})();
</script>
@endpush
@endsection
