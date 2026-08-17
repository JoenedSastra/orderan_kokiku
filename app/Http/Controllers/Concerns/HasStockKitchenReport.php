<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use Carbon\CarbonPeriod;

/**
 * Membangun laporan stok harian (Masuk, Keluar, Sisa) per barang,
 * dikelompokkan per kategori (Sayur, Daging, Saos), untuk rentang tanggal tertentu.
 *
 * Laporan dibangun otomatis dari data stock_ins & stock_outs yang sudah ada
 * (bukan input manual terpisah) supaya selalu konsisten dengan riwayat transaksi.
 */
trait HasStockKitchenReport
{
    /**
     * @return array{categories: array, startDate: string, endDate: string}
     */
    protected function buildStockKitchenReport(?string $startDate, ?string $endDate): array
    {
        $start = empty($startDate) ? now()->subDays(6) : \Carbon\Carbon::parse($startDate);
        $end   = empty($endDate) ? now() : \Carbon\Carbon::parse($endDate);

        if ($start->gt($end)) {
            $temp = $start;
            $start = $end;
            $end = $temp;
        }

        // Limit range to max 1 year (365 days) to prevent extreme DB load / execution time
        if ($start->diffInDays($end) > 365) {
            $start = $end->copy()->subDays(365);
        }

        $startDate = $start->toDateString();
        $endDate   = $end->toDateString();

        $categories = Category::whereIn('name', ['Sayur', 'Daging', 'Saos'])
            ->with(['items' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        $period = CarbonPeriod::create($startDate, $endDate);
        $report = [];

        foreach ($categories as $category) {
            $itemsReport = [];

            foreach ($category->items as $item) {
                // Saldo awal = total stok kitchen sebelum tanggal mulai
                $saldoAwal = $item->stockIns()
                        ->where('location', StockIn::LOCATION_KITCHEN)
                        ->where('tanggal', '<', $startDate)
                        ->sum('quantity')
                    - $item->stockOuts()
                        ->where('location', StockOut::LOCATION_KITCHEN)
                        ->where('tanggal', '<', $startDate)
                        ->sum('quantity');

                $masukPerHari = $item->stockIns()
                    ->where('location', StockIn::LOCATION_KITCHEN)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->selectRaw('tanggal, SUM(quantity) as total')
                    ->groupBy('tanggal')
                    ->pluck('total', 'tanggal');

                $keluarPerHari = $item->stockOuts()
                    ->where('location', StockOut::LOCATION_KITCHEN)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->selectRaw('tanggal, SUM(quantity) as total')
                    ->groupBy('tanggal')
                    ->pluck('total', 'tanggal');

                $rows = [];
                $saldo = (int) $saldoAwal;

                foreach ($period as $date) {
                    $tanggal = $date->toDateString();
                    $masuk   = (int) ($masukPerHari[$tanggal] ?? 0);
                    $keluar  = (int) ($keluarPerHari[$tanggal] ?? 0);
                    $saldo   = $saldo + $masuk - $keluar;

                    $rows[] = [
                        'tanggal' => $tanggal,
                        'masuk'   => $masuk,
                        'keluar'  => $keluar,
                        'sisa'    => $saldo,
                    ];
                }

                $itemsReport[] = [
                    'item' => $item,
                    'rows' => $rows,
                ];
            }

            $report[] = [
                'category' => $category,
                'items'    => $itemsReport,
            ];
        }

        return [
            'categories' => $report,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ];
    }
}
