@extends('layouts.app')
@section('title', 'Catat Barang Masuk Gudang')
@section('content')
<div class="mb-3"><a href="{{ route('admin.stock_in.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:560px">
    <h5 class="mb-3">Barang Datang dari Supplier</h5>
    <form action="{{ route('admin.stock_in.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                <option value="">— Tanpa Supplier —</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror"
                   value="{{ old('item_name') }}" placeholder="Ketik nama barang" required autofocus>
            @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Kalau nama ini sudah pernah diinput sebelumnya, sistem otomatis memakai data barang yang sama (tidak dobel).</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Masuk ke Master Barang <span class="text-danger">*</span></label>
            <select name="master_location" class="form-select @error('master_location') is-invalid @enderror" required>
                <option value="">— Pilih Master Barang —</option>
                <option value="gudang_utama" {{ old('master_location') == 'gudang_utama' ? 'selected' : '' }}>Gudang Utama</option>
                <option value="gudang_resto" {{ old('master_location') == 'gudang_resto' ? 'selected' : '' }}>Gudang Resto</option>
                <option value="kasir" {{ old('master_location') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="kitchen" {{ old('master_location') == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
            </select>
            @error('master_location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Kalau barangnya sudah pernah diinput sebelumnya, pilihan ini diabaikan (dipakai master barang yang sudah tersimpan).</div>
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
                    <option value="Gram"><option value="Kg"><option value="Ons">
                    <option value="Ml"><option value="Liter">
                </datalist>
                @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_completed" id="is_completed" class="form-check-input" value="1" {{ old('is_completed', true) ? 'checked' : '' }}>
            <label for="is_completed" class="form-check-label">Tandai selesai (data sudah lengkap &amp; benar)</label>
        </div>

        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan — Stok Gudang Bertambah</button>
    </form>
</div>
@endsection
