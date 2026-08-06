@extends('layouts.app')
@section('title', 'Master Barang')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Master Barang</h2>
    <a href="{{ route('admin.items.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Tambah Barang
    </a>
</div>
<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th><th>Min Stok</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-semibold">{{ $item->name }}</div>
                        @if($item->description)<small class="text-muted">{{ $item->description }}</small>@endif
                    </td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->min_stock }}</td>
                    <td>
                        <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Belum ada barang. <a href="{{ route('admin.items.create') }}">Tambah sekarang</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
</div>
@endsection
