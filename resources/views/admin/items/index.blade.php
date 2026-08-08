@extends('layouts.app')
@section('title', 'Master Barang')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header flex-wrap gap-2">
    <h2 class="h5 mb-0">Master Barang</h2>
    <div class="input-group" style="max-width:280px;">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="text" id="masterBarangSearch" class="form-control" placeholder="Cari barang...">
    </div>
</div>

<div class="kk-stat-card">
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#mb-gudang-utama">
                <i class="bi bi-building"></i> Gudang Utama
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#mb-gudang-resto">
                <i class="bi bi-shop"></i> Gudang Resto
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#mb-kasir">
                <i class="bi bi-cash-coin"></i> Kasir
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#mb-kitchen">
                <i class="bi bi-egg-fried"></i> Kitchen
            </a>
        </li>
    </ul>

    <div class="tab-content">
        @foreach($grouped as $key => $groupItems)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="mb-{{ str_replace('_', '-', $key) }}">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th class="text-end">Total Stock</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupItems as $item)
                        <tr class="kk-mb-row" data-name="{{ strtolower($item->name) }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td>{{ $item->unit }}</td>
                            <td class="text-end">
                                <span class="badge {{ $item->totalStock() <= $item->min_stock ? 'bg-danger' : 'bg-primary' }}">
                                    {{ $item->totalStock() }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.items.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Hapus barang ini? Riwayat stok terkait juga ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada barang di bagian ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('masterBarangSearch');

        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.kk-mb-row').forEach(function (row) {
                row.style.display = row.dataset.name.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@endsection
