@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('content')
<div class="mb-3"><a href="{{ route('admin.categories.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:480px">
    <h5 class="mb-3">Edit Kategori</h5>
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Perbarui</button>
    </form>
</div>
@endsection
