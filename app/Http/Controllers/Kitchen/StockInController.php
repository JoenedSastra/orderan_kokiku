<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(Request $request): View
    {
        // Tampilkan SEMUA barang masuk untuk Master Barang Kitchen — otomatis
        // sinkron dengan yang diinput Admin lewat "Barang Masuk Harian" dan
        // diklasifikasikan ke Kitchen. Kitchen tidak lagi input manual di sini.
        $query = StockIn::with(['item', 'user.role'])
            ->whereHas('item', fn ($q) => $q->where('master_location', Item::MASTER_KITCHEN))
            ->where('created_at', '>=', now()->subDays(45));

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $stockIns = $query->latest()->paginate(15);
            
        $allItemsForAdjust = Item::where('master_location', Item::MASTER_KITCHEN)
            ->whereHas('stockIns', function($q) {
                $q->where('location', StockIn::LOCATION_KITCHEN);
            })
            ->orderBy('name')
            ->get();

        return view('kitchen.stock_in.index', compact('stockIns', 'allItemsForAdjust'));
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $stockIn = StockIn::findOrFail($id);
        
        // Ensure this stock in belongs to Kitchen
        if ($stockIn->location !== StockIn::LOCATION_KITCHEN) {
            abort(403);
        }

        $stockIn->delete();
        
        // Hapus juga semua riwayat barang keluar yang berhubungan dengan item ini
        StockOut::where('item_id', $stockIn->item_id)
            ->where('location', StockIn::LOCATION_KITCHEN)
            ->delete();
        return back()->with('success', 'Riwayat barang masuk berhasil dihapus.');
    }

    public function adjustStock(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'new_stock' => 'required|array',
            'new_stock.*' => 'required|integer|min:0',
        ]);

        $userId = \Illuminate\Support\Facades\Auth::id();
        $changedCount = 0;

        foreach ($request->new_stock as $itemId => $newStock) {
            $item = Item::find($itemId);
            if (!$item || $item->master_location !== Item::MASTER_KITCHEN) continue;
            
            $currentStock = $item->stokByLocation(Item::MASTER_KITCHEN);
            $newStockInt = (int) $newStock;
            
            if ($newStockInt === $currentStock) {
                continue;
            }

            $difference = $newStockInt - $currentStock;
            $location = StockIn::LOCATION_KITCHEN;
            $keterangan = 'Penyesuaian stok manual oleh Kitchen';

            if ($difference > 0) {
                StockIn::create([
                    'item_id'      => $item->id,
                    'user_id'      => $userId,
                    'quantity'     => $difference,
                    'location'     => $location,
                    'keterangan'   => $keterangan,
                    'tanggal'      => today(),
                    'is_completed' => true,
                ]);
            } else {
                StockOut::create([
                    'item_id'    => $item->id,
                    'user_id'    => $userId,
                    'quantity'   => abs($difference),
                    'location'   => $location,
                    'keterangan' => $keterangan,
                    'tanggal'    => today(),
                ]);
            }
            
            $changedCount++;
        }

        if ($changedCount > 0) {
            return back()->with('success', $changedCount . ' barang berhasil disesuaikan stoknya.');
        }

        return back()->with('info', 'Tidak ada perubahan stok yang dilakukan.');
    }
}
