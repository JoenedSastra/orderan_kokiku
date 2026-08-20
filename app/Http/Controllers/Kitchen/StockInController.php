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
            ->select('stock_ins.*')
            ->join('items', 'stock_ins.item_id', '=', 'items.id')
            ->where('items.master_location', Item::MASTER_KITCHEN)
            ->where('stock_ins.created_at', '>=', now()->subDays(45));

        $tanggal = $request->input('tanggal', now()->toDateString());
        $query->whereDate('stock_ins.tanggal', $tanggal);

        $stockIns = $query->orderBy('items.name', 'asc')->paginate(15)->withQueryString();
            
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
            'new_masuk'        => 'required|array',
            'new_masuk.*'      => 'nullable|numeric|min:0',
            'new_keluar'       => 'required|array',
            'new_keluar.*'     => 'nullable|numeric|min:0',
            'new_stock'        => 'nullable|array',
            'new_stock.*'      => 'nullable|numeric|min:0',
            'new_keluar_unit'  => 'nullable|array',
            'new_keluar_unit.*'=> 'nullable|string|max:30',
            'new_unit'         => 'nullable|array',
            'new_unit.*'       => 'nullable|string|max:30',
            'tanggal'          => 'nullable|date',
        ]);

        $userId = \Illuminate\Support\Facades\Auth::id();
        $changedCount = 0;
        $tanggal = $request->input('tanggal', today()->toDateString());

        foreach ($request->new_masuk as $itemId => $newMasuk) {
            $item = Item::find($itemId);
            if (!$item || $item->master_location !== Item::MASTER_KITCHEN) continue;

            // Update satuan khusus Kitchen jika berubah
            $newUnit = trim($request->input("new_unit.{$itemId}", ''));
            if ($newUnit && $newUnit !== ($item->kitchen_unit ?? $item->unit)) {
                $item->kitchen_unit = $newUnit;
                $item->save();
            }
            
            $newMasukInt    = (float) str_replace(',', '.', $newMasuk);
            $newKeluarInt   = (float) str_replace(',', '.', $request->new_keluar[$itemId] ?? 0);
            $newKeluarUnit  = trim($request->input("new_keluar_unit.{$itemId}", '')) ?: ($item->kitchen_unit ?? $item->unit);
            
            $currentMasuk  = $item->masukByLocation(Item::MASTER_KITCHEN);
            $currentKeluar = $item->keluarByLocation(Item::MASTER_KITCHEN);

            $diffMasuk  = $newMasukInt - $currentMasuk;
            $diffKeluar = $newKeluarInt - $currentKeluar;

            // Simpan manual override
            $newStockStr = $request->new_stock[$itemId] ?? null;
            if ($newStockStr !== null && $newStockStr !== '') {
                $item->kitchen_stock = (float) str_replace(',', '.', $newStockStr);
            }
            $item->kitchen_keluar = $newKeluarInt;
            $item->kitchen_last_masuk = $currentMasuk;
            $item->save();

            if ($diffMasuk == 0 && $diffKeluar == 0) {
                continue;
            }

            $locationIn = StockIn::LOCATION_KITCHEN;
            $locationOut = StockOut::LOCATION_KITCHEN;
            $keterangan = 'Penyesuaian stok manual oleh Kitchen';

            \Illuminate\Support\Facades\DB::transaction(function () use ($item, $userId, $locationIn, $locationOut, $keterangan, $diffMasuk, $diffKeluar, $tanggal, $newKeluarUnit) {
                
                // --- Handle Masuk diff ---
                if ($diffMasuk > 0) {
                    StockIn::create([
                        'item_id'      => $item->id,
                        'user_id'      => $userId,
                        'quantity'     => $diffMasuk,
                        'location'     => $locationIn,
                        'keterangan'   => $keterangan,
                        'tanggal'      => $tanggal,
                        'is_completed' => true,
                    ]);
                } elseif ($diffMasuk < 0) {
                    $toReduce = abs($diffMasuk);
                    $stockIns = StockIn::where('item_id', $item->id)->where('location', $locationIn)->orderBy('id', 'desc')->get();
                    foreach ($stockIns as $si) {
                        if ($toReduce <= 0) break;
                        if ($si->quantity <= $toReduce) {
                            $toReduce -= $si->quantity;
                            $si->delete();
                        } else {
                            $si->quantity -= $toReduce;
                            $si->save();
                            $toReduce = 0;
                        }
                    }
                }

                // --- Handle Keluar diff ---
                if ($diffKeluar > 0) {
                    StockOut::create([
                        'item_id'    => $item->id,
                        'user_id'    => $userId,
                        'quantity'   => $diffKeluar,
                        'unit'       => $newKeluarUnit,
                        'location'   => $locationOut,
                        'keterangan' => $keterangan,
                        'tanggal'    => $tanggal,
                    ]);
                } elseif ($diffKeluar < 0) {
                    $toReduce = abs($diffKeluar);
                    $stockOuts = StockOut::where('item_id', $item->id)->where('location', $locationOut)->orderBy('id', 'desc')->get();
                    foreach ($stockOuts as $so) {
                        if ($toReduce <= 0) break;
                        if ($so->quantity <= $toReduce) {
                            $toReduce -= $so->quantity;
                            $so->delete();
                        } else {
                            $so->quantity -= $toReduce;
                            $so->save();
                            $toReduce = 0;
                        }
                    }
                }
            });

            $changedCount++;
        }

        if ($changedCount > 0) {
            return back()->with('success', $changedCount . ' barang berhasil disesuaikan sinkronisasi Masuk/Keluarnya.');
        }

        return back()->with('info', 'Tidak ada perubahan stok yang dilakukan.');
    }
}
