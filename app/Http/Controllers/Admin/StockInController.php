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
        Item::MASTER_KITCHEN      => 'bi-egg-fried',
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

        $stockIns = StockIn::with(['item', 'user'])
            ->whereHas('user.role', fn ($q) => $q->where('slug', Role::ADMIN))
            ->whereDate('tanggal', $tanggal->toDateString())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock_in.riwayat', compact('stockIns', 'tanggal'));
    }

    public function create(string $lokasi): View
    {
        $lokasiLabel = self::LOKASI_LABELS[$lokasi];
        $jumlahBaris = self::JUMLAH_BARIS;

        $namaBarangSuggestions = Item::where('master_location', $lokasi)
            ->orderBy('name')
            ->pluck('name');

        $satuanSuggestions = Item::where('master_location', $lokasi)
            ->distinct()
            ->orderBy('unit')
            ->pluck('unit');

        return view('admin.stock_in.create', compact('lokasi', 'lokasiLabel', 'jumlahBaris', 'namaBarangSuggestions', 'satuanSuggestions'));
    }

    public function store(Request $request, string $lokasi): RedirectResponse
    {
        $lokasiLabel = self::LOKASI_LABELS[$lokasi];

        $request->validate([
            'rows'                => 'required|array',
            'rows.*.item_name'    => 'nullable|string|max:150',
            'rows.*.quantity'     => 'nullable|integer|min:1',
            'rows.*.unit'         => 'nullable|string|max:30',
            'rows.*.keterangan'   => 'nullable|string|max:255',
        ]);

        $rowsToSave = collect($request->input('rows', []))
            ->filter(fn ($row) => filled($row['item_name'] ?? null));

        if ($rowsToSave->isEmpty()) {
            return back()->withErrors(['rows' => 'Isi minimal 1 baris sebelum kirim.'])->withInput();
        }

        foreach ($rowsToSave as $index => $row) {
            if (blank($row['quantity'] ?? null) || blank($row['unit'] ?? null)) {
                return back()
                    ->withErrors(['rows' => 'Baris ' . ((int) $index + 1) . ' ("' . $row['item_name'] . '") — Jumlah dan Satuan wajib diisi.'])
                    ->withInput();
            }
        }

        $userId      = Auth::id();
        $savedCount  = 0;
        $notifyBatch = collect();

        DB::transaction(function () use ($rowsToSave, $lokasi, $userId, &$savedCount, &$notifyBatch) {
            foreach ($rowsToSave as $row) {
                $item = Item::firstOrCreate(
                    [
                        'name'            => trim($row['item_name']),
                        'master_location' => $lokasi,
                        'unit'            => trim($row['unit']),
                    ],
                    ['min_stock' => 0]
                );

                $ledgerLocation = $item->master_location === Item::MASTER_GUDANG_UTAMA
                    ? StockIn::LOCATION_GUDANG
                    : StockIn::LOCATION_RESTORAN;

                $keteranganFinal = filled($row['keterangan'] ?? null)
                    ? trim($row['keterangan'])
                    : 'Diterima';

                $stockIn = StockIn::create([
                    'item_id'      => $item->id,
                    'supplier_id'  => null,
                    'user_id'      => $userId,
                    'quantity'     => $row['quantity'],
                    'location'     => $ledgerLocation,
                    'keterangan'   => $keteranganFinal,
                    'tanggal'      => today(),
                    'is_completed' => true,
                ]);

                $savedCount++;
                $notifyBatch->push([$item, $stockIn]);
            }
        });

        foreach ($notifyBatch as [$item, $stockIn]) {
            $this->notifyStockIn($item, $stockIn);
        }

        return redirect()
            ->to($this->stockPageUrl($lokasi))
            ->with('success', $savedCount . ' barang berhasil dicatat & otomatis masuk ke Stok ' . $lokasiLabel . '.');
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
