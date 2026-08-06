@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('content')
<div class="mb-3"><a href="{{ route('admin.suppliers.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:520px">
    <h5 class="mb-3">Tambah Supplier</h5>
    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Supplier</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan</button>
    </form>
</div>
@endsection
