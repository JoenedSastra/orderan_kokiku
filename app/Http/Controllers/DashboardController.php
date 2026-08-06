<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $user              = Auth::user();
        $permintaanMenunggu = Order::where('status', Order::STATUS_MENUNGGU)->count();
        $totalBarang       = Item::count();
        $totalUser         = User::count();

        // Barang stok rendah (stok kasir + kitchen gabungan < min_stock)
        $stokRendah = Item::all()->filter(function ($item) {
            $masuk  = StockIn::where('item_id', $item->id)->sum('quantity');
            $keluar = StockOut::where('item_id', $item->id)->sum('quantity');
            return ($masuk - $keluar) <= $item->min_stock;
        })->count();

        $ordersRecent = Order::with(['item', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'user', 'permintaanMenunggu', 'totalBarang', 'totalUser', 'stokRendah', 'ordersRecent'
        ));
    }

    public function kasir(): View
    {
        $user   = Auth::user();
        $userId = $user->id;

        $permintaanMenunggu = Order::where('user_id', $userId)
            ->where('status', Order::STATUS_MENUNGGU)->count();

        $keluarHariIni = StockOut::where('user_id', $userId)
            ->whereDate('tanggal', today())->sum('quantity');

        // Hitung barang stok rendah untuk kasir
        $stokRendah = Item::all()->filter(function ($item) use ($userId) {
            $masuk  = StockIn::where('item_id', $item->id)->where('user_id', $userId)->sum('quantity');
            $keluar = StockOut::where('item_id', $item->id)->where('user_id', $userId)->sum('quantity');
            return ($masuk - $keluar) <= $item->min_stock && $item->min_stock > 0;
        })->count();

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

        $stokRendah = Item::all()->filter(function ($item) use ($userId) {
            $masuk  = StockIn::where('item_id', $item->id)->where('user_id', $userId)->sum('quantity');
            $keluar = StockOut::where('item_id', $item->id)->where('user_id', $userId)->sum('quantity');
            return ($masuk - $keluar) <= $item->min_stock && $item->min_stock > 0;
        })->count();

        $ordersRecent = Order::with('item')
            ->where('user_id', $userId)
            ->latest()->limit(5)->get();

        return view('dashboard.kitchen', compact(
            'user', 'permintaanMenunggu', 'keluarHariIni', 'stokRendah', 'ordersRecent'
        ));
    }
}
