@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('content')
<div class="mb-3"><a href="{{ route('admin.items.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:560px">
    <h5 class="mb-3">Tambah Barang</h5>
    <form action="{{ route('admin.items.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">— Tanpa Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                    @foreach(['Pcs','Kg','Liter','Box','Pack','Lusin','Ikat','Botol','Karton','Gram'] as $u)
                    <option value="{{ $u }}" {{ old('unit','Pcs') == $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
                @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-3">
                <label class="form-label">Min Stok</label>
                <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', 0) }}" min="0">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan</button>
    </form>
</div>
@endsection
