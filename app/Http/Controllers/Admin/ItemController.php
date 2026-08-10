<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatatanTemplate;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Master Barang — read-only + hapus + kirim barang dari Gudang Utama.
 * Barang baru masuk otomatis lewat form "Barang Masuk Gudang"
 * (Admin\StockInController), dikelompokkan ke salah satu dari 4 Master
 * Barang: Gudang Utama, Gudang Resto, Kasir, atau Kitchen.
 */
class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::orderBy('name')->get();

        $grouped = [
            'gudang_utama' => $items->where('master_location', Item::MASTER_GUDANG_UTAMA)->values(),
            'gudang_resto' => $items->where('master_location', Item::MASTER_GUDANG_RESTO)->values(),
            'kasir'        => $items->where('master_location', Item::MASTER_KASIR)->values(),
            'kitchen'      => $items->where('master_location', Item::MASTER_KITCHEN)->values(),
        ];

        // Murni catatan yang PERNAH diketik manual oleh admin — tidak ada teks
        // otomatis/sistem ("Kirim dari Gudang Utama ke ...", dst) yang ikut jadi saran.
        $keteranganSuggestions = CatatanTemplate::orderBy('teks')->get(['id', 'teks']);

        return view('admin.items.index', compact('grouped', 'keteranganSuggestions'));
    }

    /**
     * Hapus satu catatan dari daftar saran Keterangan. Dipanggil lewat AJAX
     * dari tombol "x" di dropdown saran — tidak memengaruhi keterangan yang
     * sudah tersimpan di riwayat StockIn/StockOut manapun, cuma menghapus
     * saran-nya saja dari daftar pilihan berikutnya.
     */
    public function destroyKeteranganSuggestion(CatatanTemplate $catatanTemplate): \Illuminate\Http\JsonResponse
    {
        $catatanTemplate->delete();

        return response()->json(['success' => true]);
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * Kirim barang dari Gudang Utama ke Gudang Resto / Kasir / Kitchen.
     * Stok Gudang Utama berkurang, dan otomatis dibuat/ditambahkan ke Master
     * Barang tujuan dengan nama & satuan yang sama.
     */
    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'item_id'     => 'required|exists:items,id',
            'destination' => 'required|in:gudang_resto,kasir,kitchen',
            'quantity'    => 'required|integer|min:1',
            'tanggal'     => 'required|date',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $sourceItem = Item::findOrFail($request->item_id);

        if ($sourceItem->master_location !== Item::MASTER_GUDANG_UTAMA) {
            return back()->withErrors(['item_id' => 'Barang ini bukan barang Gudang Utama.'])->withInput();
        }

        $stokGudang = $sourceItem->stokGudang();

        if ($request->quantity > $stokGudang) {
            return back()->withErrors([
                'quantity' => 'Stok Gudang Utama tidak mencukupi. Stok tersedia: ' . $stokGudang . ' ' . $sourceItem->unit,
            ])->withInput();
        }

        $destinationLabels = [
            'gudang_resto' => 'Gudang Resto',
            'kasir'        => 'Kasir',
            'kitchen'      => 'Kitchen',
        ];

        DB::transaction(function () use ($request, $sourceItem, $destinationLabels) {
            $userId      = Auth::id();
            $catatanBaku = $request->filled('keterangan') ? trim($request->keterangan) : null;

            // Simpan catatan mentah (bukan teks otomatis) ke daftar saran,
            // supaya bisa dipilih lagi lain kali tanpa perlu ketik ulang.
            if ($catatanBaku) {
                CatatanTemplate::firstOrCreate(['teks' => $catatanBaku]);
            }

            // Keterangan: pakai persis apa yang diketik admin (dipakai sama
            // di kedua sisi — Gudang Utama & tujuan). Kalau admin tidak
            // mengisi apa-apa, baru pakai default berikut supaya tidak
            // pernah dobel seperti "Diterima — Diterima".
            $keteranganGudang = $catatanBaku ?: ('Kirim ke ' . $destinationLabels[$request->destination]);
            $keteranganTujuan = $catatanBaku ?: 'Diterima';

            // Kurangi stok Gudang Utama pada barang sumber.
            StockOut::create([
                'item_id'    => $sourceItem->id,
                'user_id'    => $userId,
                'quantity'   => $request->quantity,
                'location'   => StockOut::LOCATION_GUDANG,
                'keterangan' => $keteranganGudang,
                'tanggal'    => $request->tanggal,
            ]);

            // Cari/buat barang tujuan dengan nama & satuan yang sama.
            $targetItem = Item::firstOrCreate(
                ['name' => $sourceItem->name, 'master_location' => $request->destination],
                ['unit' => $sourceItem->unit, 'min_stock' => 0]
            );

            StockIn::create([
                'item_id'      => $targetItem->id,
                'user_id'      => $userId,
                'quantity'     => $request->quantity,
                'location'     => StockIn::LOCATION_RESTORAN,
                'keterangan'   => $keteranganTujuan,
                'tanggal'      => $request->tanggal,
                'is_completed' => true,
            ]);
        });

        return back()->with('success', $sourceItem->name . ' berhasil dikirim ke ' . $destinationLabels[$request->destination] . '.');
    }
}
