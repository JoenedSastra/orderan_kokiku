@extends('layouts.app')
@section('title', 'Catat Barang Keluar')
@section('content')

<div style="max-width:520px;">

    {{-- Card utama --}}
    <div class="border-0 shadow-sm" style="border-radius:16px;overflow:hidden;background:#fff;">

        {{-- Header gradient --}}
        <div style="background:linear-gradient(135deg,#ea580c 0%,#f97316 60%,#fb923c 100%);padding:1.25rem 1.5rem 2rem;">
            <h5 class="mb-0 fw-bold text-white" style="font-size:1rem;">Catat Barang Keluar</h5>
            <small style="color:rgba(255,255,255,.75);font-size:.78rem;">Kasir</small>
        </div>

        {{-- Form body --}}
        <div class="p-4" style="margin-top:-1rem;">
            <form action="{{ route('kasir.stock_out.store') }}" method="POST" id="formStockOut">
                @csrf

                {{-- Barang --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                        <i class="bi bi-box-seam me-1" style="color:#ea580c;"></i>Barang
                    </label>
                    <select name="item_id" id="so_item_id"
                            class="form-select @error('item_id') is-invalid @enderror"
                            style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;" required>
                        <option value="">Pilih Barang</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                    @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Jumlah & Tanggal --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                            <i class="bi bi-123 me-1" style="color:#ea580c;"></i>Jumlah
                        </label>
                        <input type="number" name="quantity" id="so_quantity"
                               class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', 1) }}" min="1"
                               style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;text-align:center;" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                            <i class="bi bi-calendar3 me-1" style="color:#ea580c;"></i>Tanggal
                        </label>
                        <input type="date" name="tanggal" id="so_tanggal"
                               class="form-control @error('tanggal') is-invalid @enderror"
                               value="{{ old('tanggal', date('Y-m-d')) }}"
                               style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;" required>
                        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:.85rem;color:#374151;">
                        <i class="bi bi-chat-left-text me-1" style="color:#ea580c;"></i>Keterangan
                    </label>
                    <input type="text" name="keterangan" id="so_keterangan"
                           class="form-control"
                           value="{{ old('keterangan') }}"
                           placeholder="Digunakan, Dijual, dll"
                           style="border-radius:10px;border-color:#d1d5db;font-size:.875rem;">
                </div>

                {{-- Tombol bersebelahan --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('kasir.stock_out.index') }}"
                       class="btn fw-semibold px-4"
                       style="border-radius:10px;border:1.5px solid #fca5a5;color:#dc2626;font-size:.875rem;background:#fff1f2;flex-shrink:0;">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <button type="submit"
                            class="btn fw-semibold flex-grow-1"
                            style="background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;border:none;border-radius:10px;font-size:.875rem;transition:.2s;"
                            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
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
