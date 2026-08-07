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
                ->orderBy('tanggal')
                ->get()
                ->map(fn ($s) => [
                    'tanggal'    => $s->tanggal->format('d-m-Y'),
                    'barang'     => $s->item->name,
                    'jumlah'     => $s->quantity,
                    'satuan'     => $s->item->unit,
                    'lokasi'     => ucfirst($s->location),
                    'supplier'   => $s->supplier?->name ?? '-',
                    'oleh'       => $s->user->name,
                    'keterangan' => $s->keterangan ?? '-',
                ]),

            'barang_keluar' => StockOut::with(['item', 'user'])
                ->whereBetween('tanggal', [$startDate, $endDate])
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

            'permintaan' => Order::with(['item', 'user', 'approvedBy'])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->orderBy('created_at')
                ->get()
                ->map(fn ($o) => [
                    'tanggal'  => $o->created_at->format('d-m-Y'),
                    'dari'     => $o->user->name . ' (' . $o->user->role?->name . ')',
                    'barang'   => $o->item->name,
                    'jumlah'   => $o->quantity,
                    'satuan'   => $o->item->unit,
                    'status'   => strtoupper($o->status),
                    'diproses' => $o->approvedBy?->name ?? '-',
                ]),

            'stock_kitchen' => collect($this->buildStockKitchenReport($startDate, $endDate)['categories'])
                ->flatMap(fn ($cat) => collect($cat['items'])->flatMap(
                    fn ($itemReport) => collect($itemReport['rows'])->map(fn ($row) => [
                        'tanggal'  => \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y'),
                        'kategori' => $cat['category']->name,
                        'barang'   => $itemReport['item']->name,
                        'satuan'   => $itemReport['item']->unit,
                        'masuk'    => $row['masuk'],
                        'keluar'   => $row['keluar'],
                        'sisa'     => $row['sisa'],
                    ])
                )),

            default => collect(),
        };
    }

    protected function reportLabel(string $type): string
    {
        return match ($type) {
            'barang_masuk'  => 'Laporan Barang Masuk',
            'barang_keluar' => 'Laporan Barang Keluar',
            'permintaan'    => 'Laporan Permintaan',
            'stock_kitchen' => 'Laporan Stock Kitchen',
            default         => 'Laporan',
        };
    }

    /**
     * @return array<int, string>
     */
    protected function reportHeadings(string $type): array
    {
        return match ($type) {
            'barang_masuk'  => ['Tanggal', 'Barang', 'Jumlah', 'Satuan', 'Lokasi', 'Supplier', 'Oleh', 'Keterangan'],
            'barang_keluar' => ['Tanggal', 'Barang', 'Jumlah', 'Satuan', 'Lokasi', 'Oleh', 'Keterangan'],
            'permintaan'    => ['Tanggal', 'Dari', 'Barang', 'Jumlah', 'Satuan', 'Status', 'Diproses Oleh'],
            'stock_kitchen' => ['Tanggal', 'Kategori', 'Barang', 'Satuan', 'Masuk', 'Keluar', 'Sisa'],
            default         => [],
        };
    }
}
