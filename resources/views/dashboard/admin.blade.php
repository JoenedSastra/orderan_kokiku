@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<p class="text-muted mb-4">Selamat datang, {{ $user->name }}.</p>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value text-warning">{{ $permintaanMenunggu }}</div>
                <div class="kk-stat-label">Permintaan Menunggu</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-hourglass-split"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value text-danger">{{ $stokRendah }}</div>
                <div class="kk-stat-label">Barang Stok Rendah</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">{{ $totalBarang }}</div>
                <div class="kk-stat-label">Total Master Barang</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kk-stat-card d-flex align-items-start justify-content-between">
            <div>
                <div class="kk-stat-value">{{ $totalUser }}</div>
                <div class="kk-stat-label">Total User Aktif</div>
            </div>
            <div class="kk-stat-icon"><i class="bi bi-people"></i></div>
        </div>
    </div>
</div>

<div class="kk-stat-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-semibold">Permintaan Barang Terbaru</div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
    </div>
    @if($ordersRecent->isEmpty())
        <p class="text-muted mb-0" style="font-size:0.88rem;">Belum ada permintaan barang.</p>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Dari</th><th>Barang</th><th>Jumlah</th><th>Status</th><th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordersRecent as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->item->name }}</td>
                        <td>{{ $order->quantity }} {{ $order->item->unit }}</td>
                        <td><span class="kk-badge-{{ $order->status }}">{{ strtoupper($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('d-m-Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="kk-stat-card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-semibold">Grafik Barang Masuk &amp; Keluar</div>
        <span class="text-muted" style="font-size:0.8rem;">7 hari terakhir</span>
    </div>
    <canvas id="chartStokMasukKeluar" height="90"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('chartStokMasukKeluar');

        new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: @json($chartMasuk),
                        backgroundColor: '#0f766e',
                        borderRadius: 6,
                        maxBarThickness: 32,
                    },
                    {
                        label: 'Barang Keluar',
                        data: @json($chartKeluar),
                        backgroundColor: '#dc2626',
                        borderRadius: 6,
                        maxBarThickness: 32,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });
    });
</script>
@endsection
