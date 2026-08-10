@extends('layouts.app')
@section('title', 'Catat Barang Masuk Hari Ini')
@section('content')
<div class="kk-stat-card" style="max-width:560px">
    <h5 class="mb-3">Catat Barang Masuk Hari Ini</h5>
    <form action="{{ route('admin.stock_in.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror"
                   value="{{ old('item_name') }}" placeholder="Ketik nama barang" required autofocus>
            @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Barang dengan nama, Master Barang, & satuan yang sama (persis) akan memakai data yang sudah ada (stok tidak dobel). Kalau nama sama tapi Master Barang atau satuannya beda, akan dianggap barang terpisah — nanti saat dicari namanya akan muncul semua sesuai satuannya masing-masing.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Pilih Master Barang <span class="text-danger">*</span></label>
            <select name="master_location" class="form-select @error('master_location') is-invalid @enderror" required>
                <option value="">— Pilih Master Barang —</option>
                <option value="gudang_utama" {{ old('master_location') == 'gudang_utama' ? 'selected' : '' }}>Gudang Utama</option>
                <option value="gudang_resto" {{ old('master_location') == 'gudang_resto' ? 'selected' : '' }}>Gudang Resto</option>
                <option value="kasir" {{ old('master_location') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="kitchen" {{ old('master_location') == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
            </select>
            @error('master_location')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                <input type="text" name="unit" list="unitOptions" class="form-control @error('unit') is-invalid @enderror"
                       value="{{ old('unit') }}" placeholder="Pilih atau ketik sendiri" required>
                <datalist id="unitOptions">
                    <option value="Pcs"><option value="Buah"><option value="Botol"><option value="Kaleng">
                    <option value="Pack"><option value="Bungkus"><option value="Karton"><option value="Box">
                    <option value="Saset"><option value="Renteng"><option value="Jeriken">
                    <option value="Gram"><option value="Kg"><option value="Ons">
                    <option value="Ml"><option value="Liter">
                </datalist>
                @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3 small text-muted">
            <i class="bi bi-clock-history"></i>
            Tanggal &amp; jam otomatis diisi sesuai waktu saat ini: <strong id="liveDateTime">{{ now()->format('d-m-Y, H:i') }}</strong>
        </div>

        <div class="mb-3">
            <label class="form-label">Catatan Tambahan</label>
            <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                   value="{{ old('keterangan') }}" placeholder="Opsional — akan tersimpan sebagai: Diterima — catatan ini">
            <div class="form-text">Keterangan otomatis tersimpan sebagai "Diterima". Isi kolom ini kalau mau menambahkan catatan tambahan di belakangnya.</div>
            @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan</button>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const el = document.getElementById('liveDateTime');
    if (!el) return;

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function render() {
        const d = new Date();
        const tanggal = pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear();
        const jam = pad(d.getHours()) + ':' + pad(d.getMinutes());
        el.textContent = tanggal + ', ' + jam;
    }

    render();
    setInterval(render, 1000);
})();
</script>
@endpush
@endsection
