@extends('layouts.app')
@section('title', 'Kategori Barang')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Kategori Barang</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
    </a>
</div>

<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nama Kategori</th><th>Digunakan Oleh</th><th>Jumlah Barang</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cat->name }}</td>
                    <td>
                        @if($cat->used_by === 'kasir')
                            <span class="badge bg-info text-dark">Kasir</span>
                        @elseif($cat->used_by === 'kitchen')
                            <span class="badge bg-warning text-dark">Kitchen</span>
                        @else
                            <span class="badge bg-secondary">Umum</span>
                        @endif
                    </td>
                    <td>{{ $cat->items_count }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-xs btn-outline-secondary btn-sm">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $categories->links() }}</div>
</div>
@endsection
