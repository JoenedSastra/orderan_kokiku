<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $user = Auth::user();

        $permintaanMenunggu  = Order::where('status', Order::STATUS_MENUNGGU)->count();
        $permintaanDisetujui = Order::where('status', Order::STATUS_DISETUJUI)->count();
        $permintaanDitolak   = Order::where('status', Order::STATUS_DITOLAK)->count();

        $totalBarang   = Item::count();
        $totalUser     = User::count();
        $totalSupplier = Supplier::count();

        $masukHariIni  = (int) StockIn::whereDate('tanggal', today())->sum('quantity');
        $keluarHariIni = (int) StockOut::whereDate('tanggal', today())->sum('quantity');

        // Barang stok rendah = saldo Restoran <= min_stock (butuh direstock dari Gudang)
        $stokRendah = Item::all()
            ->filter(fn ($item) => $item->min_stock > 0 && $item->stokRestoran() <= $item->min_stock)
            ->count();

        // Gabungkan order dari semua role (kasir + kitchen + admin), latest 10
        $ordersRecent = Order::with(['item', 'user', 'user.role'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'user', 'permintaanMenunggu', 'permintaanDisetujui', 'permintaanDitolak',
            'totalBarang', 'totalUser', 'totalSupplier', 'masukHariIni', 'keluarHariIni',
            'stokRendah', 'ordersRecent'
        ));
    }

    /**
     * Endpoint AJAX: data grafik bulanan (Barang Masuk, Barang Keluar, Permintaan) untuk 1 tahun.
     * Dipanggil dari dashboard.admin via fetch().
     */
    public function chartData(Request $request): JsonResponse
    {
        $year = (int) $request->query('year', now()->year);

        $bulanLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $masukPerBulan = StockIn::whereYear('tanggal', $year)
            ->selectRaw('MONTH(tanggal) as bulan, SUM(quantity) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $keluarPerBulan = StockOut::whereYear('tanggal', $year)
            ->selectRaw('MONTH(tanggal) as bulan, SUM(quantity) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $permintaanPerBulan = Order::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $masuk = $keluar = $permintaan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $masuk[]      = (int) ($masukPerBulan[$bulan] ?? 0);
            $keluar[]     = (int) ($keluarPerBulan[$bulan] ?? 0);
            $permintaan[] = (int) ($permintaanPerBulan[$bulan] ?? 0);
        }

        return response()->json([
            'labels'     => $bulanLabel,
            'masuk'      => $masuk,
            'keluar'     => $keluar,
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Endpoint AJAX: data grafik harian (per jam, hari ini).
     * Mengembalikan data per jam (00-23) untuk tanggal hari ini.
     */
    public function chartDataHarian(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $masukPerJam = StockIn::whereDate('tanggal', $date)
            ->selectRaw('HOUR(created_at) as jam, SUM(quantity) as total')
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $keluarPerJam = StockOut::whereDate('tanggal', $date)
            ->selectRaw('HOUR(created_at) as jam, SUM(quantity) as total')
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $permintaanPerJam = Order::whereDate('created_at', $date)
            ->selectRaw('HOUR(created_at) as jam, COUNT(*) as total')
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $masuk = $keluar = $permintaan = $labels = [];

        // Tampilkan jam 06:00 - 22:00 (rentang operasional)
        for ($jam = 0; $jam <= 23; $jam++) {
            $labels[]     = sprintf('%02d:00', $jam);
            $masuk[]      = (int) ($masukPerJam[$jam] ?? 0);
            $keluar[]     = (int) ($keluarPerJam[$jam] ?? 0);
            $permintaan[] = (int) ($permintaanPerJam[$jam] ?? 0);
        }

        return response()->json([
            'labels'     => $labels,
            'masuk'      => $masuk,
            'keluar'     => $keluar,
            'permintaan' => $permintaan,
            'date'       => $date,
        ]);
    }

    /**
     * Endpoint AJAX: data grafik tahunan (per tahun, rentang 5 tahun terakhir).
     */
    public function chartDataTahunan(Request $request): JsonResponse
    {
        $currentYear = now()->year;
        $startYear   = $currentYear - 4; // 5 tahun terakhir

        $masukPerTahun = StockIn::whereBetween(
                \DB::raw('YEAR(tanggal)'), [$startYear, $currentYear]
            )
            ->selectRaw('YEAR(tanggal) as tahun, SUM(quantity) as total')
            ->groupBy('tahun')
            ->pluck('total', 'tahun');

        $keluarPerTahun = StockOut::whereBetween(
                \DB::raw('YEAR(tanggal)'), [$startYear, $currentYear]
            )
            ->selectRaw('YEAR(tanggal) as tahun, SUM(quantity) as total')
            ->groupBy('tahun')
            ->pluck('total', 'tahun');

        $permintaanPerTahun = Order::whereBetween(
                \DB::raw('YEAR(created_at)'), [$startYear, $currentYear]
            )
            ->selectRaw('YEAR(created_at) as tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->pluck('total', 'tahun');

        $masuk = $keluar = $permintaan = $labels = [];

        for ($tahun = $startYear; $tahun <= $currentYear; $tahun++) {
            $labels[]     = (string) $tahun;
            $masuk[]      = (int) ($masukPerTahun[$tahun] ?? 0);
            $keluar[]     = (int) ($keluarPerTahun[$tahun] ?? 0);
            $permintaan[] = (int) ($permintaanPerTahun[$tahun] ?? 0);
        }

        return response()->json([
            'labels'     => $labels,
            'masuk'      => $masuk,
            'keluar'     => $keluar,
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Endpoint AJAX: data grafik stok barang per divisi.
     * Mengembalikan label nama barang dan stok per lokasi (master_location).
     */
    public function divisionStockData(Request $request): JsonResponse
    {
        $division = $request->query('division', 'gudang_utama');

        $items = Item::where('master_location', $division)->get();

        $labels    = [];
        $stockData = [];

        foreach ($items as $item) {
            $labels[]    = $item->name;
            $stockData[] = $item->totalStock();
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $stockData,
        ]);
    }

    public function kasir(): View
    {
        $user   = Auth::user();
        $userId = $user->id;

        $permintaanMenunggu = Order::where('user_id', $userId)
            ->where('status', Order::STATUS_MENUNGGU)->count();

        $keluarHariIni = StockOut::where('user_id', $userId)
            ->whereDate('tanggal', today())->sum('quantity');

        // Barang stok rendah di Resto (saldo bersama Kasir & Kitchen)
        $stokRendah = Item::all()
            ->filter(fn ($item) => $item->min_stock > 0 && $item->stokRestoran() <= $item->min_stock)
            ->count();

        $ordersRecent = Order::with('item')
            ->where('user_id', $userId)
            ->latest()->limit(5)->get();

        return view('dashboard.kasir', compact(
            'user', 'permintaanMenunggu', 'keluarHariIni', 'stokRendah', 'ordersRecent'
        ));
    }

    public function kitchen(): View
    {
        $user   = Auth::user();
        $userId = $user->id;

        $permintaanMenunggu = Order::where('user_id', $userId)
            ->where('status', Order::STATUS_MENUNGGU)->count();

        $keluarHariIni = StockOut::where('user_id', $userId)
            ->whereDate('tanggal', today())->sum('quantity');

        $stokRendah = Item::all()
            ->filter(fn ($item) => $item->min_stock > 0 && $item->stokRestoran() <= $item->min_stock)
            ->count();

        $ordersRecent = Order::with('item')
            ->where('user_id', $userId)
            ->latest()->limit(5)->get();

        return view('dashboard.kitchen', compact(
            'user', 'permintaanMenunggu', 'keluarHariIni', 'stokRendah', 'ordersRecent'
        ));
    }
}
