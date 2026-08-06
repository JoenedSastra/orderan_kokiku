@extends('layouts.app')
@section('title', 'Stock Barang')
@section('content')
<h2 class="h5 mb-3">Stock Barang — Semua Lokasi</h2>

<ul class="nav nav-tabs mb-3" id="stockTab">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#masuk">Barang Masuk</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#keluar">Barang Keluar</a></li>
</ul>

<div class="tab-content kk-stat-card">
    {{-- Tab Masuk --}}
    <div class="tab-pane fade show active" id="masuk">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Keterangan</th><th>Oleh</th></tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $s)
                    <tr>
                        <td>{{ $s->tanggal->format('d-m-Y') }}</td>
                        <td>{{ $s->item->name }}</td>
                        <td>{{ $s->quantity }} {{ $s->item->unit }}</td>
                        <td>{{ $s->keterangan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $s->user->role?->name ?? '?' }}</span>
                            {{ $s->user->name }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $stockIns->links() }}</div>
    </div>

    {{-- Tab Keluar --}}
    <div class="tab-pane fade" id="keluar">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Keterangan</th><th>Oleh</th></tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $s)
                    <tr>
                        <td>{{ $s->tanggal->format('d-m-Y') }}</td>
                        <td>{{ $s->item->name }}</td>
                        <td>{{ $s->quantity }} {{ $s->item->unit }}</td>
                        <td>{{ $s->keterangan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $s->user->role?->name ?? '?' }}</span>
                            {{ $s->user->name }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $stockOuts->links() }}</div>
    </div>
</div>
@endsection
