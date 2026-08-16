<?php

namespace App\Http\Controllers\Kasir;

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
        // Tampilkan SEMUA barang masuk untuk Master Barang Kasir — otomatis
        // sinkron dengan yang diinput Admin lewat "Barang Masuk Harian" dan
        // diklasifikasikan ke Kasir. Kasir tidak lagi input manual di sini.
        $query = StockIn::with(['item', 'user.role'])
            ->select('stock_ins.*')
            ->join('items', 'stock_ins.item_id', '=', 'items.id')
            ->where('items.master_location', Item::MASTER_KASIR)
            ->where('stock_ins.created_at', '>=', now()->subDays(45));

        $tanggal = $request->input('tanggal', now()->toDateString());
        $query->whereDate('stock_ins.tanggal', $tanggal);

        $stockIns = $query->orderBy('items.name', 'asc')->paginate(15)->withQueryString();
            
        $allItemsForAdjust = Item::where('master_location', Item::MASTER_KASIR)
            ->whereHas('stockIns', function($q) {
                $q->where('location', StockIn::LOCATION_KASIR);
            })
            ->orderBy('name')
            ->get();

        return view('kasir.stock_in.index', compact('stockIns', 'allItemsForAdjust'));
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $stockIn = StockIn::findOrFail($id);
        
        // Ensure this stock in belongs to Kasir
        if ($stockIn->location !== StockIn::LOCATION_KASIR) {
            abort(403);
        }

        $stockIn->delete();
        
        // Hapus juga semua riwayat barang keluar yang berhubungan dengan item ini
        StockOut::where('item_id', $stockIn->item_id)
            ->where('location', StockIn::LOCATION_KASIR)
            ->delete();
        return back()->with('success', 'Riwayat barang masuk berhasil dihapus.');
    }

    public function adjustStock(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'new_stock' => 'required|array',
            'new_stock.*' => 'required|integer|min:0',
            'tanggal' => 'nullable|date',
        ]);

        $userId = \Illuminate\Support\Facades\Auth::id();
        $changedCount = 0;
        $tanggal = $request->input('tanggal', today()->toDateString());

        foreach ($request->new_stock as $itemId => $newStock) {
            $item = Item::find($itemId);
            if (!$item || $item->master_location !== Item::MASTER_KASIR) continue;
            
            $keterangan = 'Penyesuaian stok manual oleh Kasir';
            $location = StockIn::LOCATION_KASIR;

            // Hapus penyesuaian sebelumnya pada tanggal yang sama untuk item ini
            StockIn::where('item_id', $item->id)
                ->where('location', $location)
                ->whereDate('tanggal', $tanggal)
                ->where('keterangan', $keterangan)
                ->forceDelete();

            StockOut::where('item_id', $item->id)
                ->where('location', $location)
                ->whereDate('tanggal', $tanggal)
                ->where('keterangan', $keterangan)
                ->forceDelete();

            $currentStock = $item->stokByLocation(Item::MASTER_KASIR);
            $newStockInt = (int) $newStock;
            
            if ($newStockInt === $currentStock) {
                continue;
            }

            $difference = $newStockInt - $currentStock;

            if ($difference > 0) {
                StockIn::create([
                    'item_id'      => $item->id,
                    'user_id'      => $userId,
                    'quantity'     => $difference,
                    'location'     => $location,
                    'keterangan'   => $keterangan,
                    'tanggal'      => $tanggal,
                    'is_completed' => true,
                ]);
            } else {
                StockOut::create([
                    'item_id'    => $item->id,
                    'user_id'    => $userId,
                    'quantity'   => abs($difference),
                    'location'   => $location,
                    'keterangan' => $keterangan,
                    'tanggal'    => $tanggal,
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
