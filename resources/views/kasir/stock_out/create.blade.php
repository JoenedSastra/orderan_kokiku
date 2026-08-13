@extends('layouts.app')
@section('title', 'Catat Barang Keluar')
@section('content')
<div class="mb-3"><a href="{{ route('kasir.stock_out.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:520px">
    <h5 class="mb-3">Catat Barang Keluar</h5>
    <form action="{{ route('kasir.stock_out.store') }}" method="POST" id="formStockOut">
        @csrf
        <div class="mb-3">
            <label class="form-label">Barang <span class="text-danger">*</span></label>
            <select name="item_id" id="so_item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                <option value="">— Pilih Barang —</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->unit }})</option>
                @endforeach
            </select>
            @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="quantity" id="so_quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" id="so_tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="so_keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Digunakan, Dijual, dll">
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan</button>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const DRAFT_KEY = 'kk_so_draft';
    const form = document.getElementById('formStockOut');
    const fields = {
        item_id    : document.getElementById('so_item_id'),
        quantity   : document.getElementById('so_quantity'),
        tanggal    : document.getElementById('so_tanggal'),
        keterangan : document.getElementById('so_keterangan'),
    };

    // Muat draft tersimpan (jika tidak ada nilai dari server `old()`)
    function loadDraft() {
        const saved = localStorage.getItem(DRAFT_KEY);
        if (!saved) return;
        try {
            const data = JSON.parse(saved);
            Object.keys(fields).forEach(function (key) {
                // Jangan timpa nilai yang sudah diisi server (old())
                if (fields[key] && !fields[key].value && data[key]) {
                    fields[key].value = data[key];
                }
            });
        } catch (e) {
            localStorage.removeItem(DRAFT_KEY);
        }
    }

    function saveDraft() {
        const data = {};
        Object.keys(fields).forEach(function (key) {
            data[key] = fields[key] ? fields[key].value : '';
        });
        localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
    }

    loadDraft();

    // Simpan draft setiap kali ada perubahan
    Object.values(fields).forEach(function (field) {
        if (field) field.addEventListener('change', saveDraft);
    });
    form.addEventListener('input', saveDraft);

    // Hapus draft setelah submit berhasil
    form.addEventListener('submit', function () {
        clearDraft();
    });
})();
</script>
@endpush
@endsection
