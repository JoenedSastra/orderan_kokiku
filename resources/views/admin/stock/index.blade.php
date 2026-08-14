@extends('layouts.app')
@section('title', 'Stok Barang - ' . $title)
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2 class="h5 mb-0">Stok Barang — {{ $title }}</h2>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @if($title === 'Gudang Utama')
        <button type="button" class="btn btn-sm text-white text-nowrap" style="background:#2563eb;" data-bs-toggle="modal" data-bs-target="#modalKirimBarang">
            <i class="bi bi-send-fill"></i> Kirim Barang
        </button>
        <a href="{{ route('admin.stock.riwayat_terkirim') }}" class="btn btn-sm text-white text-nowrap" style="background:#16a34a;">
            <i class="bi bi-clock-history"></i> Riwayat Terkirim
        </a>
        @endif
        <div class="kk-search-box">
            <i class="bi bi-search"></i>
            <input type="text" name="kk_search" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang..." autocomplete="off">
        </div>
    </div>
</div>

<div class="kk-stat-card p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Hari, Jam, &amp; Tanggal</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Master</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockIns as $s)
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center">{{ $s->created_at->translatedFormat('l, d M Y H:i') }}</td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $s->item->name }}</td>
                    <td class="text-center">{{ $s->quantity }}</td>
                    <td class="text-center">{{ $s->item->unit }}</td>
                    <td class="text-center"><span class="badge bg-success">{{ $s->item->masterLocationLabel() }}</span></td>
                    <td class="text-center">{{ $s->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $s->user->role?->name ?? '?' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stockIns->hasPages())
    <div class="p-3">{{ $stockIns->links() }}</div>
    @endif
</div>

@if($title === 'Gudang Utama')
<div class="modal fade" id="modalKirimBarang" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.stock.kirim') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Barang dari Gudang Utama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                        <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">— Pilih Barang —</option>
                            @foreach($itemsGudangUtama as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} (Stok: {{ $item->stokGudang() }} {{ $item->unit }})
                            </option>
                            @endforeach
                        </select>
                        @error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kirim ke <span class="text-danger">*</span></label>
                        <select name="destination" class="form-select @error('destination') is-invalid @enderror" required>
                            <option value="">— Pilih Divisi —</option>
                            <option value="gudang_resto">Gudang Resto</option>
                            <option value="kasir">Kasir</option>
                            <option value="kitchen">Kitchen</option>
                        </select>
                        @error('destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" min="1" value="1" class="form-control @error('quantity') is-invalid @enderror" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <select name="keterangan" class="form-select @error('keterangan') is-invalid @enderror" required>
                            <option value="">— Pilih Keterangan —</option>
                            <option value="Gudang Resto">Gudang Resto</option>
                            <option value="Kasir">Kasir</option>
                            <option value="Kitchen">Kitchen</option>
                        </select>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="background:#2563eb;">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
