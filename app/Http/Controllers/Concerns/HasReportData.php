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

            'stock_gudang_utama' => \App\Models\Item::with(['stockIns.user.role', 'stockOuts.user.role'])
                ->where('master_location', \App\Models\Item::MASTER_GUDANG_UTAMA)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereHas('stockIns', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->orWhereHas('stockOuts', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    });
                })
                ->orderBy('name')
                ->get()
                ->map(function ($item) use ($startDate, $endDate) {
                    $aktivitas = $item->latestActivity();
                    return [
                        'tanggal'      => $aktivitas?->created_at?->translatedFormat('l, d M Y H:i') ?? '-',
                        'barang'       => $item->name,
                        'masuk'        => $item->masukByLocation($item->master_location, $startDate, $endDate),
                        'keluar'       => $item->keluarByLocation($item->master_location, $startDate, $endDate),
                        'sisa'         => $item->stokByLocation($item->master_location, $endDate),
                        'satuan'       => $item->unit,
                        'master'       => $item->masterLocationLabel(),
                        'keterangan'   => $aktivitas?->keterangan ?? '-',
                        'dicatat_oleh' => $aktivitas?->user?->role?->name ?? '-',
                    ];
                }),

            'stock_gudang_resto' => \App\Models\Item::with(['stockIns.user.role', 'stockOuts.user.role'])
                ->where('master_location', \App\Models\Item::MASTER_GUDANG_RESTO)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereHas('stockIns', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->orWhereHas('stockOuts', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    });
                })
                ->orderBy('name')
                ->get()
                ->map(function ($item) use ($startDate, $endDate) {
                    $aktivitas = $item->latestActivity();
                    return [
                        'tanggal'      => $aktivitas?->created_at?->translatedFormat('l, d M Y H:i') ?? '-',
                        'barang'       => $item->name,
                        'masuk'        => $item->masukByLocation($item->master_location, $startDate, $endDate),
                        'keluar'       => $item->keluarByLocation($item->master_location, $startDate, $endDate),
                        'sisa'         => $item->stokByLocation($item->master_location, $endDate),
                        'satuan'       => $item->unit,
                        'master'       => $item->masterLocationLabel(),
                        'keterangan'   => $aktivitas?->keterangan ?? '-',
                        'dicatat_oleh' => $aktivitas?->user?->role?->name ?? '-',
                    ];
                }),

            'stock_kasir' => \App\Models\Item::with(['stockIns.user.role', 'stockOuts.user.role'])
                ->where('master_location', \App\Models\Item::MASTER_KASIR)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereHas('stockIns', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->orWhereHas('stockOuts', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    });
                })
                ->orderBy('name')
                ->get()
                ->map(function ($item) use ($startDate, $endDate) {
                    $aktivitas = $item->latestActivity();
                    return [
                        'tanggal'      => $aktivitas?->created_at?->translatedFormat('l, d M Y H:i') ?? '-',
                        'barang'       => $item->name,
                        'masuk'        => $item->masukByLocation($item->master_location, $startDate, $endDate),
                        'keluar'       => $item->keluarByLocation($item->master_location, $startDate, $endDate),
                        'sisa'         => $item->stokByLocation($item->master_location, $endDate),
                        'satuan'       => $item->unit,
                        'master'       => $item->masterLocationLabel(),
                        'keterangan'   => $aktivitas?->keterangan ?? '-',
                        'dicatat_oleh' => $aktivitas?->user?->role?->name ?? '-',
                    ];
                }),

            'stock_kitchen' => \App\Models\Item::with(['stockIns.user.role', 'stockOuts.user.role'])
                ->where('master_location', \App\Models\Item::MASTER_KITCHEN)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereHas('stockIns', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })->orWhereHas('stockOuts', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    });
                })
                ->orderBy('name')
                ->get()
                ->map(function ($item) use ($startDate, $endDate) {
                    $aktivitas = $item->latestActivity();
                    return [
                        'tanggal'      => $aktivitas?->created_at?->translatedFormat('l, d M Y H:i') ?? '-',
                        'barang'       => $item->name,
                        'masuk'        => $item->masukByLocation($item->master_location, $startDate, $endDate),
                        'keluar'       => $item->keluarByLocation($item->master_location, $startDate, $endDate),
                        'sisa'         => $item->stokByLocation($item->master_location, $endDate),
                        'satuan'       => $item->unit,
                        'master'       => $item->masterLocationLabel(),
                        'keterangan'   => $aktivitas?->keterangan ?? '-',
                        'dicatat_oleh' => $aktivitas?->user?->role?->name ?? '-',
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
            'stock_gudang_utama'    => 'Laporan Stock Gudang Utama',
            'stock_gudang_resto'    => 'Laporan Stock Gudang Resto',
            'stock_kasir'           => 'Laporan Stock Kasir',
            'stock_kitchen'         => 'Laporan Stock Kitchen',
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
            'stock_gudang_utama'    => ['Hari, Jam & Tanggal', 'Nama Barang', 'Masuk', 'Keluar', 'Stock', 'Satuan', 'Devisi', 'Keterangan', 'Dicatat Oleh'],
            'stock_gudang_resto'    => ['Hari, Jam & Tanggal', 'Nama Barang', 'Masuk', 'Keluar', 'Stock', 'Satuan', 'Devisi', 'Keterangan', 'Dicatat Oleh'],
            'stock_kasir'           => ['Hari, Jam & Tanggal', 'Nama Barang', 'Masuk', 'Keluar', 'Stock', 'Satuan', 'Devisi', 'Keterangan', 'Dicatat Oleh'],
            'stock_kitchen'         => ['Hari, Jam & Tanggal', 'Nama Barang', 'Masuk', 'Keluar', 'Stock', 'Satuan', 'Devisi', 'Keterangan', 'Dicatat Oleh'],
            default         => [],
        };
    }
}
