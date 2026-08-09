<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Role;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\StockInNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

/**
 * Barang Masuk Gudang — Admin mencatat penerimaan barang dari Supplier.
 * Nama barang diketik bebas: kalau kombinasi (nama + Master Barang) belum
 * pernah ada, otomatis dibuat baru. Barang dengan nama sama tapi Master
 * Barang berbeda dianggap barang terpisah (stok tidak tercampur).
 */
class StockInController extends Controller
{
    public function index(): View
    {
        // Tampilkan semua barang masuk yang dicatat Admin lewat form ini,
        // apapun Master Barang tujuannya (Gudang Utama/Resto/Kasir/Kitchen
        // sama-sama tercatat di sini). Kasir & Kitchen punya pencatatan
        // stok masuk sendiri (menulis ke lokasi "restoran" juga), jadi
        // di-filter berdasarkan role pencatatnya supaya tidak ikut tercampur.
        $stockIns = StockIn::with(['item', 'supplier', 'user'])
            ->whereHas('user.role', fn ($q) => $q->where('slug', Role::ADMIN))
            ->latest()
            ->paginate(15);

        return view('admin.stock_in.index', compact('stockIns'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.stock_in.create', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'item_name'       => 'required|string|max:150',
            'unit'            => 'required|string|max:30',
            'master_location' => 'required|in:gudang_utama,gudang_resto,kasir,kitchen',
            'quantity'        => 'required|integer|min:1',
            'keterangan'      => 'nullable|string|max:255',
            'tanggal'         => 'required|date',
        ]);

        // Kunci pencocokan barang = nama + Master Barang. Jadi "Bakso Sapi" di
        // Kitchen dan "Bakso Sapi" di Gudang Utama tetap 2 barang yang
        // terpisah, walaupun namanya sama persis — stoknya tidak tercampur.
        $item = Item::firstOrCreate(
            ['name' => trim($request->item_name), 'master_location' => $request->master_location],
            [
                'unit'      => $request->unit,
                'min_stock' => 0,
            ]
        );

        // Gudang Utama disimpan di ledger lokasi "gudang". Gudang Resto, Kasir,
        // dan Kitchen sama-sama berbagi ledger lokasi "restoran" (sesuai alur
        // Supplier -> Gudang -> Restoran -> Kasir/Kitchen), supaya stokRestoran()
        // ikut bertambah dan otomatis muncul di Stock Keseluruhan Barang.
        $ledgerLocation = $item->master_location === Item::MASTER_GUDANG_UTAMA
            ? StockIn::LOCATION_GUDANG
            : StockIn::LOCATION_RESTORAN;

        $stockIn = StockIn::create([
            'item_id'      => $item->id,
            'supplier_id'  => $request->supplier_id,
            'user_id'      => Auth::id(),
            'quantity'     => $request->quantity,
            'location'     => $ledgerLocation,
            'keterangan'   => $request->keterangan,
            'tanggal'      => $request->tanggal,
            'is_completed' => $request->boolean('is_completed'),
        ]);

        $this->notifyStockIn($item, $stockIn);

        return redirect()->route('admin.stock_in.index')
            ->with('success', 'Barang masuk gudang berhasil dicatat & otomatis masuk ke Master Barang ' . $item->masterLocationLabel() . '.');
    }

    /**
     * Beritahu Kasir/Kitchen kalau ada barang baru masuk ke bagian mereka.
     * Gudang Utama & Gudang Resto tidak dikirimi notifikasi lintas role
     * karena keduanya memang murni dikelola Admin sendiri.
     */
    private function notifyStockIn(Item $item, StockIn $stockIn): void
    {
        $roleSlug = match ($item->master_location) {
            Item::MASTER_KASIR   => Role::KASIR,
            Item::MASTER_KITCHEN => Role::KITCHEN,
            default              => null,
        };

        if ($roleSlug === null) {
            return;
        }

        $recipients = User::whereHas('role', fn ($q) => $q->where('slug', $roleSlug))->get();
        Notification::send($recipients, new StockInNotification($stockIn));
    }
}
