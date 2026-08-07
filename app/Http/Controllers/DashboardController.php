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

        $ordersRecent = Order::with(['item', 'user'])
            ->latest()
            ->limit(5)
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
