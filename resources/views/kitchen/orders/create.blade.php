@extends('layouts.app')
@section('title', 'Buat Permintaan Barang')
@section('content')
<div class="mb-3"><a href="{{ route('kitchen.orders.index') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali</a></div>
<div class="kk-stat-card" style="max-width:520px">
    <h5 class="mb-3">Buat Permintaan Barang ke Admin</h5>
    <form action="{{ route('kitchen.orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Barang yang Diminta <span class="text-danger">*</span></label>
            <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                <option value="">— Pilih Barang —</option>
                @foreach($items as $item)
                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->unit }})</option>
                @endforeach
            </select>
            @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1" required>
            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Alasan / keterangan tambahan...">
        </div>
        <div class="alert alert-info py-2" style="font-size:0.86rem;">
            <i class="bi bi-info-circle"></i> Permintaan akan dikirim ke Admin dengan status <strong>MENUNGGU</strong>.
            Setelah Admin menyetujui, stock barang akan otomatis bertambah.
        </div>
        <button type="submit" class="btn text-white w-100" style="background:var(--kk-accent)">Kirim Permintaan</button>
    </form>
</div>
@endsection
