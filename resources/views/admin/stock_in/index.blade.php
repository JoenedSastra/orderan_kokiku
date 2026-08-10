@extends('layouts.app')
@section('title', 'Barang Masuk Hari Ini')
@section('content')

@php
$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$isToday = $tanggal->toDateString() === today()->toDateString();
@endphp

<div class="d-flex justify-content-between align-items-center mb-3 kk-page-header">
    <h2 class="h5 mb-0">Barang Masuk Hari Ini</h2>
    <a href="{{ route('admin.stock_in.create') }}" class="btn btn-sm text-white" style="background:var(--kk-accent)">
        <i class="bi bi-plus-lg"></i> Catat Masuk
    </a>
</div>

<form method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div class="input-group" style="max-width:210px;">
        <span class="input-group-text bg-white"><i class="bi bi-calendar-date"></i></span>
        <input type="date" name="tanggal" value="{{ $tanggal->toDateString() }}" class="form-control" onchange="this.form.submit()">
    </div>
    @unless($isToday)
    <a href="{{ route('admin.stock_in.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-counterclockwise"></i> Reset ke Hari Ini
    </a>
    @endunless
    <span class="text-muted small ms-1">
        Menampilkan: {{ $hariIndo[$tanggal->format('l')] }}, {{ $tanggal->format('d-m-Y') }}
        @if($isToday)<span class="badge" style="background:var(--kk-success);">Hari Ini</span>@endif
    </span>
</form>

<div class="kk-stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Master</th>
                    <th>Keterangan</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockIns as $s)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->item->name }}</td>
                    <td>{{ $s->quantity }}</td>
                    <td>{{ $s->item->unit }}</td>
                    <td><span class="badge bg-secondary">{{ $s->item->masterLocationLabel() }}</span></td>
                    <td>
                        {{ $s->keterangan ?? '-' }}
                        @if($s->is_completed)
                            <i class="bi bi-check-circle-fill text-success ms-1" title="Selesai"></i>
                        @endif
                    </td>
                    <td>{{ $s->user->name }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Belum ada barang masuk pada tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $stockIns->links() }}</div>
</div>
@endsection
