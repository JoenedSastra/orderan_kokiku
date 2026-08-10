<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Role;
use App\Models\StockIn;
use App\Models\User;
use App\Notifications\StockInNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

/**
 * Barang Masuk Hari Ini — Admin mencatat penerimaan barang dari Supplier.
 * Nama barang diketik bebas: kalau kombinasi (nama + Master Barang + satuan)
 * belum pernah ada, otomatis dibuat baru. Barang dengan nama sama tapi Master
 * Barang ATAU satuan berbeda dianggap barang terpisah (stok tidak tercampur).
 * Tanggal & jam selalu otomatis mengikuti waktu saat data dicatat.
 */
class StockInController extends Controller
{
    public function index(Request $request): View
    {
        // Default tampilkan hari ini saja ("Barang Masuk Hari Ini"). Admin
        // bisa pilih tanggal lain lewat filter tanggal untuk menengok histori,
        // lalu tombol Reset akan mengembalikan ke hari ini.
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : today();

        $stockIns = StockIn::with(['item', 'user'])
            ->whereHas('user.role', fn ($q) => $q->where('slug', Role::ADMIN))
            ->whereDate('tanggal', $tanggal->toDateString())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock_in.index', compact('stockIns', 'tanggal'));
    }

    public function create(): View
    {
        return view('admin.stock_in.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'item_name'       => 'required|string|max:150',
            'unit'            => 'required|string|max:30',
            'master_location' => 'required|in:gudang_utama,gudang_resto,kasir,kitchen',
            'quantity'        => 'required|integer|min:1',
            'keterangan'      => 'nullable|string|max:255',
        ]);

        // Kunci pencocokan barang = nama + Master Barang + satuan. Jadi
        // "Milo" satuan Renteng dan "Milo" satuan Pack tetap 2 barang yang
        // terpisah walaupun nama & Master Barang-nya sama — stoknya baru
        // digabung kalau nama, Master Barang, DAN satuannya sama persis.
        $item = Item::firstOrCreate(
            [
                'name'            => trim($request->item_name),
                'master_location' => $request->master_location,
                'unit'            => $request->unit,
            ],
            [
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

        // Keterangan: pakai persis apa yang diketik admin. Kalau admin tidak
        // mengisi apa-apa, baru pakai default "Diterima" — supaya tidak pernah
        // dobel kalau admin sendiri kebetulan mengetik "Diterima".
        $keteranganFinal = $request->filled('keterangan')
            ? trim($request->keterangan)
            : 'Diterima';

        $stockIn = StockIn::create([
            'item_id'      => $item->id,
            'supplier_id'  => null,
            'user_id'      => Auth::id(),
            'quantity'     => $request->quantity,
            'location'     => $ledgerLocation,
            'keterangan'   => $keteranganFinal,
            'tanggal'      => today(),
            'is_completed' => true,
        ]);

        $this->notifyStockIn($item, $stockIn);

        return redirect()->route('admin.stock_in.index')
            ->with('success', 'Barang masuk hari ini berhasil dicatat & otomatis masuk ke Master Barang ' . $item->masterLocationLabel() . '.');
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
