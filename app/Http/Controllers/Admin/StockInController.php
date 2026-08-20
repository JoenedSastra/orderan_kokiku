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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class StockInController extends Controller
{
    public const LOKASI_LABELS = [
        Item::MASTER_GUDANG_UTAMA => 'Gudang Utama',
        Item::MASTER_GUDANG_RESTO => 'Gudang Resto',
        Item::MASTER_KASIR        => 'Kasir',
        Item::MASTER_KITCHEN      => 'Kitchen',
    ];

    private const LOKASI_ICONS = [
        Item::MASTER_GUDANG_UTAMA => 'bi-building',
        Item::MASTER_GUDANG_RESTO => 'bi-shop',
        Item::MASTER_KASIR        => 'bi-cash-coin',
        Item::MASTER_KITCHEN      => 'bi-cup-hot-fill',
    ];

    private const LOKASI_GRADIENTS = [
        Item::MASTER_GUDANG_UTAMA => 'gradient-orange',
        Item::MASTER_GUDANG_RESTO => 'gradient-green',
        Item::MASTER_KASIR        => 'gradient-red',
        Item::MASTER_KITCHEN      => 'gradient-amber',
    ];

    private const JUMLAH_BARIS = 25;

    public function index(): View
    {
        $today = today();

        // Hitung jumlah item yang dicatat hari ini per lokasi (oleh admin)
        $totalsPerLokasi = StockIn::whereDate('tanggal', $today)
            ->whereHas('user.role', fn ($q) => $q->where('slug', Role::ADMIN))
            ->join('items', 'stock_ins.item_id', '=', 'items.id')
            ->selectRaw('items.master_location, COUNT(*) as total')
            ->groupBy('items.master_location')
            ->pluck('total', 'items.master_location');

        $lokasiList = [];
        foreach (self::LOKASI_LABELS as $key => $label) {
            $lokasiList[] = [
                'key'      => $key,
                'label'    => $label,
                'icon'     => self::LOKASI_ICONS[$key],
                'gradient' => self::LOKASI_GRADIENTS[$key],
                'total'    => $totalsPerLokasi[$key] ?? 0,
            ];
        }

        return view('admin.stock_in.index', compact('lokasiList', 'today'));
    }

    public function riwayat(Request $request): View
    {
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : today();

        $lokasiFilter = $request->input('lokasi'); // null = semua

        $query = StockIn::with(['item', 'user'])
            ->select('stock_ins.*')
            ->join('items', 'stock_ins.item_id', '=', 'items.id')
            ->whereDate('stock_ins.tanggal', $tanggal->toDateString())
            ->orderBy('stock_ins.created_at', 'desc');

        if ($lokasiFilter) {
            $query->where('items.master_location', $lokasiFilter);
        }

        $stockIns = $query->paginate(15)->withQueryString();

        return view('admin.stock_in.riwayat', compact('stockIns', 'tanggal'));
    }

    public function create(string $lokasi): View
    {
        $lokasiLabel = self::LOKASI_LABELS[$lokasi];
        $jumlahBaris = self::JUMLAH_BARIS;
        $existingItems = \App\Models\Item::where('master_location', $lokasi)->orderBy('name')->get();

        return view('admin.stock_in.create', compact('lokasi', 'lokasiLabel', 'jumlahBaris', 'existingItems'));
    }

    public function store(Request $request, string $lokasi): RedirectResponse
    {
        $lokasiLabel = self::LOKASI_LABELS[$lokasi];

        $request->validate([
            'rows'                => 'required|array',
            'rows.*.item_name'    => 'nullable|string|max:150',
            'rows.*.quantity'     => 'nullable|numeric|min:0.01',
            'rows.*.unit'         => 'nullable|string|max:30',
            'rows.*.keterangan'   => 'nullable|string|max:255',
        ]);

        $rowsWithNames = collect($request->input('rows', []))
            ->filter(fn ($row) => filled($row['item_name'] ?? null));

        if ($rowsWithNames->isEmpty()) {
            return back()->withErrors(['rows' => 'Isi minimal nama barang untuk menyimpannya ke master list.'])->withInput();
        }

        foreach ($rowsWithNames as $index => $row) {
            // Jika quantity diisi, maka unit wajib diisi
            if (filled($row['quantity'] ?? null) && blank($row['unit'] ?? null)) {
                return back()
                    ->withErrors(['rows' => 'Baris "' . $row['item_name'] . '" — Satuan wajib diisi karena jumlah barang dimasukkan.'])
                    ->withInput();
            }
        }

        $userId      = Auth::id();
        $savedCount  = 0;
        $notifyBatch = collect();

        DB::transaction(function () use ($rowsWithNames, $lokasi, $userId, &$savedCount, &$notifyBatch) {
            foreach ($rowsWithNames as $row) {
                // Cari item berdasarkan nama dan lokasi (jangan masukkan unit ke kriteria pencarian)
                $item = Item::firstOrCreate(
                    [
                        'name'            => trim($row['item_name']),
                        'master_location' => $lokasi,
                    ],
                    [
                        'unit'      => trim($row['unit'] ?? ''),
                        'min_stock' => 0
                    ]
                );

                // Jika admin mengisi satuan baru yang berbeda dengan master list, perbarui satuannya
                if (filled($row['unit'] ?? null) && $item->unit !== trim($row['unit'])) {
                    $item->update(['unit' => trim($row['unit'])]);
                }

                // Hanya buat riwayat barang masuk jika jumlahnya diisi & > 0
                if (filled($row['quantity'] ?? null) && (float)str_replace(',', '.', $row['quantity']) > 0) {

                    $ledgerLocation = match ($item->master_location) {
                        Item::MASTER_KASIR   => StockIn::LOCATION_KASIR,
                        Item::MASTER_KITCHEN => StockIn::LOCATION_KITCHEN,
                        default              => StockIn::LOCATION_GUDANG_UTAMA,
                    };

                    $keteranganFinal = filled($row['keterangan'] ?? null)
                        ? trim($row['keterangan'])
                        : 'Diterima';

                    $quantityFinal = (float) str_replace(',', '.', $row['quantity']);

                    $stockIn = StockIn::create([
                        'item_id'      => $item->id,
                        'user_id'      => $userId,
                        'tanggal'      => today(),
                        'quantity'     => $quantityFinal,
                        'keterangan'   => $keteranganFinal,
                        'location'     => $ledgerLocation,
                    ]);
                    
                    $savedCount++;
                    $notifyBatch->push([$item, $stockIn]);
                }
            }
        });

        foreach ($notifyBatch as [$item, $stockIn]) {
            $this->notifyStockIn($item, $stockIn);
        }

        return redirect()
            ->to($this->stockPageUrl($lokasi))
            ->with('success', $savedCount . ' barang berhasil dicatat & otomatis masuk ke Stok ' . $lokasiLabel . '.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->withErrors(['ids' => 'Pilih minimal 1 item untuk dihapus.']);
        }

        $deleted     = 0;
        $itemIdsKena = [];

        foreach ($ids as $id) {
            $stockIn = StockIn::find($id);
            if (!$stockIn) continue;

            $itemIdsKena[] = $stockIn->item_id;
            $stockIn->delete();
            $deleted++;
        }


        return back()->with('success', $deleted . ' data berhasil dihapus.');
    }

    private function stockPageUrl(string $lokasi): string
    {
        return match ($lokasi) {
            Item::MASTER_GUDANG_UTAMA, Item::MASTER_GUDANG_RESTO
                => route('admin.stock.index', ['filter' => $lokasi]),
            Item::MASTER_KASIR, Item::MASTER_KITCHEN
                => route('admin.stock_kasir_kitchen.index', ['filter' => $lokasi]),
        };
    }

    private function notifyStockIn(Item $item, StockIn $stockIn): void
    {
        $roleSlug = null;
        if ($stockIn->location === Item::MASTER_KASIR) {
            $roleSlug = Role::KASIR;
        } elseif ($stockIn->location === Item::MASTER_KITCHEN) {
            $roleSlug = Role::KITCHEN;
        }

        if ($roleSlug) {
            $recipients = User::whereHas('role', fn ($q) => $q->where('slug', $roleSlug))->get();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new StockInNotification($stockIn));
            }
        }
    }

    public function destroyItem($id): \Illuminate\Http\JsonResponse
    {
        $item = Item::find($id);
        if ($item) {
            // Hapus semua riwayat barang masuk/keluar untuk item ini jika diperlukan 
            // agar bisa terhapus total sesuai permintaan user.
            \App\Models\StockIn::where('item_id', $item->id)->delete();
            $item->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
