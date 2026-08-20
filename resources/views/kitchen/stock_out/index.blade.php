@extends('layouts.app')
@section('title', 'Stock Barang Keluar')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header flex-wrap gap-2">
    <h2 class="h5 mb-0">Stock Barang Keluar</h2>
    <div class="d-flex align-items-center gap-2 flex-wrap">

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
                    <th class="text-center">Hari, Jam &amp; Tanggal</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Keterangan</th>
                    <th class="text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOuts as $s)
                <tr>
                    <td class="text-center"><strong>{{ $loop->iteration }}</strong></td>
                    <td class="text-center">{{ $s->created_at->translatedFormat('l, H:i, d-m-Y') }}</td>
                    <td class="text-center fw-bold" data-search="nama-barang">{{ $s->item->name }}</td>
                    <td class="text-center">{{ $s->quantity }}</td>
                    <td class="text-center">{{ $s->item->kitchen_keluar_unit ?? $s->item->kitchen_unit ?? $s->item->unit }}</td>
                    <td class="text-center">{{ $s->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        @php
                            $rn = $s->user->role?->name ?? '?';
                            if ($rn === 'Kitchen') $rn = 'Staff Dapur';
                            if ($rn === 'Kasir') $rn = 'Staff Kasir';
                        @endphp
                        @if($rn === 'Admin')
                            <span class="badge" style="background:#bbf7d0;color:#15803d;font-weight:600;">{{ $rn }}</span>
                        @elseif(in_array($rn, ['Staff Kasir', 'Staff Dapur']))
                            <span class="badge" style="background:#bae6fd;color:#0369a1;font-weight:600;">{{ $rn }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $rn }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada catatan keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stockOuts->hasPages())
    <div class="p-3">{{ $stockOuts->links() }}</div>
    @endif
</div>
@endsection
