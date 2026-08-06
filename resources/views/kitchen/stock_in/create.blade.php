@extends('layouts.app')
@section('title', 'Catat Barang Masuk')
@section('content')
<div class="mb-3"><a href="{{ route('kitchen.stock_in.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:520px">
    <h5 class="mb-3">Catat Barang Masuk</h5>
    <form action="{{ route('kitchen.stock_in.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Barang <span class="text-danger">*</span></label>
            <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
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
                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Pengiriman, Pembelian, dll">
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan</button>
    </form>
</div>
@endsection
