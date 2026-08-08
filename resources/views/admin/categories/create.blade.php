@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('content')
<div class="mb-3"><a href="{{ route('admin.categories.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:480px">
    <h5 class="mb-3">Tambah Kategori</h5>
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Digunakan Oleh</label>
            <select name="used_by" class="form-select @error('used_by') is-invalid @enderror" required>
                <option value="kitchen" {{ old('used_by') == 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                <option value="kasir" {{ old('used_by') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="umum" {{ old('used_by') == 'umum' ? 'selected' : '' }}>Umum (Kasir & Kitchen)</option>
            </select>
            @error('used_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Menentukan barang di kategori ini dihitung ke Stok Kasir atau Stok Kitchen.</div>
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan</button>
    </form>
</div>
@endsection
