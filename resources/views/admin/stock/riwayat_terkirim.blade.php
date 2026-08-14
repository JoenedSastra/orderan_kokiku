@extends('layouts.app')
@section('title', 'Riwayat Terkirim - Gudang Utama')
@section('content')

<div class="mb-3 kk-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.stock.index', ['filter' => 'gudang_utama']) }}" class="btn btn-danger btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 class="h5 mb-0">Riwayat Terkirim — Gudang Utama</h2>
    </div>
    <div class="kk-search-box">
        <i class="bi bi-search"></i>
        <input type="text" name="kk_search" class="form-control form-control-sm kk-search-nama-barang" placeholder="Cari nama barang..." autocomplete="off">
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
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $r)
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center">{{ $r->created_at->translatedFormat('l, d M Y H:i') }}</td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $r->item->name }}</td>
                    <td class="text-center">{{ $r->quantity }}</td>
                    <td class="text-center">{{ $r->item->unit }}</td>
                    <td class="text-center">{{ $r->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $r->user->role?->name ?? '?' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada barang yang dikirim dari Gudang Utama.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($riwayat->hasPages())
    <div class="p-3">{{ $riwayat->links() }}</div>
    @endif
</div>
@endsection
