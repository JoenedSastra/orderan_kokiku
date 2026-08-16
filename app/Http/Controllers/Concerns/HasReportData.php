<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Order;
use App\Models\StockIn;
use App\Models\StockOut;

/**
 * Menyediakan data 4 jenis laporan: Barang Masuk, Barang Keluar, Permintaan,
 * dan Stock Kitchen — dipakai bersama oleh ReportController (tampilan & PDF)
 * dan ReportExport (Excel), supaya logikanya tidak dobel.
 */
trait HasReportData
{
    use HasStockKitchenReport;

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function fetchReportRows(string $type, string $startDate, string $endDate)
    {
        return match ($type) {
            'barang_masuk' => StockIn::with(['item', 'user', 'supplier'])
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where(function ($q) {
                    $q->whereNull('keterangan')
                      ->orWhere('keterangan', 'not like', '%Penyesuaian stok manual%');
                })
                ->orderBy('tanggal')
                ->get()
                ->map(fn ($s) => [
                    'tanggal'    => $s->tanggal->format('d-m-Y'),
                    'barang'     => $s->item->name,
                    'jumlah'     => $s->quantity,
                    'satuan'     => $s->item->unit,
                    'lokasi'     => ucfirst($s->location),
                    'oleh'       => $s->user->name,
                    'keterangan' => $s->keterangan ?? '-',
                ]),





            'barang_keluar_kitchen' => \App\Models\StockOut::with(['item', 'user.role'])
                ->where('location', \App\Models\StockOut::LOCATION_KITCHEN)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where(function ($q) {
                    $q->whereNull('keterangan')
                      ->orWhere('keterangan', 'not like', '%Penyesuaian stok manual%');
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($stockOut) {
                    return [
                        'tanggal'      => $stockOut->created_at->translatedFormat('l, H:i, d-m-Y'),
                        'barang'       => $stockOut->item->name,
                        'jumlah'       => $stockOut->quantity,
                        'satuan'       => $stockOut->item->unit,
                        'keterangan'   => $stockOut->keterangan ?? '-',
                        'dicatat_oleh' => 'Kitchen',
                    ];
                }),

            'barang_keluar_kasir' => \App\Models\StockOut::with(['item', 'user.role'])
                ->where('location', \App\Models\StockOut::LOCATION_KASIR)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where(function ($q) {
                    $q->whereNull('keterangan')
                      ->orWhere('keterangan', 'not like', '%Penyesuaian stok manual%');
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($stockOut) {
                    return [
                        'tanggal'      => $stockOut->created_at->translatedFormat('l, H:i, d-m-Y'),
                        'barang'       => $stockOut->item->name,
                        'jumlah'       => $stockOut->quantity,
                        'satuan'       => $stockOut->item->unit,
                        'keterangan'   => $stockOut->keterangan ?? '-',
                        'dicatat_oleh' => 'Kasir',
                    ];
                }),

            default => collect(),
        };
    }

    protected function reportLabel(string $type): string
    {
        return match ($type) {
            'barang_masuk'  => 'Laporan Barang Masuk Harian',


            'barang_keluar_kitchen' => 'Laporan Barang Keluar Kitchen',
            'barang_keluar_kasir'   => 'Laporan Barang Keluar Kasir',
            default         => 'Laporan',
        };
    }

    /**
     * @return array<int, string>
     */
    protected function reportHeadings(string $type): array
    {
        return match ($type) {
            'barang_masuk'  => ['Tanggal', 'Barang', 'Jumlah', 'Satuan', 'Devisi', 'Oleh', 'Keterangan'],


            'barang_keluar_kitchen' => ['Hari, Jam & Tanggal', 'Nama Barang', 'Jumlah', 'Satuan', 'Keterangan', 'Dicatat Oleh'],
            'barang_keluar_kasir'   => ['Hari, Jam & Tanggal', 'Nama Barang', 'Jumlah', 'Satuan', 'Keterangan', 'Dicatat Oleh'],
            default         => [],
        };
    }
}
