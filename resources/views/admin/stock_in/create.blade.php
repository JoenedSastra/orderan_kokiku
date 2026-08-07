@extends('layouts.app')
@section('title', 'Catat Barang Masuk Gudang')
@section('content')
<div class="mb-3"><a href="{{ route('admin.stock_in.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:560px">
    <h5 class="mb-3">Barang Datang dari Supplier</h5>
    <form action="{{ route('admin.stock_in.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Supplier <span class="text-danger">*</span></label>
            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                <option value="">— Pilih Supplier —</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if($suppliers->isEmpty())
                <div class="form-text text-danger">Belum ada supplier. <a href="{{ route('admin.suppliers.create') }}">Tambah supplier dulu</a>.</div>
            @endif
        </div>
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
                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="No. surat jalan, catatan, dll">
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Simpan — Stok Gudang Bertambah</button>
    </form>
</div>
@endsection
