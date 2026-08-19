@extends('layouts.app')
@section('title', 'Data Stock 4 Devisi')

@section('content')
<div class="container-fluid mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 mb-0">Data Stock Barang Per Devisi</h2>
    </div>

    <div class="row g-4">
        <!-- Gudang Utama -->
        <div class="col-md-6">
            <div class="card kk-card">
                <div class="card-header text-white text-center fw-bold" style="background-color: #1e3a8a;">
                    Tabel Gudang Utama
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 10%">NO</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important;">NAMA BARANG</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 25%">STOCK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gudangUtama as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle fw-bold">{{ $item->name }}</td>
                                    <td class="text-center align-middle fw-bold" style="color:#0284c7 !important;">{{ $item->stock }}</td>
                                </tr>
                                @endforeach
                                @if($gudangUtama->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gudang Resto -->
        <div class="col-md-6">
            <div class="card kk-card">
                <div class="card-header text-white text-center fw-bold" style="background-color: #1e3a8a;">
                    Tabel Gudang Resto
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 10%">NO</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important;">NAMA BARANG</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 25%">STOCK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gudangResto as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle fw-bold">{{ $item->name }}</td>
                                    <td class="text-center align-middle fw-bold" style="color:#0284c7 !important;">{{ $item->stock }}</td>
                                </tr>
                                @endforeach
                                @if($gudangResto->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kasir -->
        <div class="col-md-6">
            <div class="card kk-card">
                <div class="card-header text-white text-center fw-bold" style="background-color: #1e3a8a;">
                    Tabel Kasir
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 10%">NO</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important;">NAMA BARANG</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 25%">STOCK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kasir as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle fw-bold">{{ $item->name }}</td>
                                    <td class="text-center align-middle fw-bold" style="color:#0284c7 !important;">{{ $item->stock }}</td>
                                </tr>
                                @endforeach
                                @if($kasir->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kitchen -->
        <div class="col-md-6">
            <div class="card kk-card">
                <div class="card-header text-white text-center fw-bold" style="background-color: #1e3a8a;">
                    Tabel Kitchen
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 10%">NO</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important;">NAMA BARANG</th>
                                    <th style="background-color: #ffb703 !important; color: #000 !important; width: 25%">STOCK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kitchen as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle fw-bold">{{ $item->name }}</td>
                                    <td class="text-center align-middle fw-bold" style="color:#0284c7 !important;">{{ $item->stock }}</td>
                                </tr>
                                @endforeach
                                @if($kitchen->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data stock.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
