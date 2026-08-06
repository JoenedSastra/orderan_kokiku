@extends('layouts.app')
@section('title', 'Supplier')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Daftar Supplier</h2>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Tambah Supplier
    </a>
</div>
<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nama</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->phone ?? '-' }}</td>
                    <td>{{ $s->address ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.suppliers.edit', $s) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.suppliers.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada supplier.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $suppliers->links() }}</div>
</div>
@endsection
