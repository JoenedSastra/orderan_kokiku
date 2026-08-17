<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Role;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tahun awal grafik "Aktivitas per Tahun". Rentang grafik akan selalu
     * mulai dari tahun ini sampai tahun berjalan (now()->year), dan akan
     * OTOMATIS bertambah satu kolom setiap tahun baru berjalan — tidak
     * perlu diubah lagi tiap tahun.
     */
    public const TAHUN_MULAI_GRAFIK = 2026;

    /**
     * Query dasar "Barang Masuk" untuk grafik/statistik dashboard.
     *
     * Aturan bisnis: hanya barang masuk yang di-INPUT LANGSUNG OLEH ADMIN
     * (lewat form Admin > Barang Masuk Harian) yang dihitung. Ini berlaku
     * untuk SEMUA divisi stock (Gudang Utama, Gudang Resto, Kasir, Kitchen)
     * sekaligus — karena form admin memang bisa input ke lokasi manapun.
     *
     * Catatan: StockIn yang otomatis tercipta saat Admin approve Permintaan
     * (lihat Admin\OrderController::approve) TIDAK dihitung di sini, karena
     * user_id pada record itu adalah requester (Kasir/Kitchen), bukan admin.
     * Pergerakan itu sudah terwakili di grafik "Barang Keluar" (lihat bawah).
     */
    private function baseMasukQuery()
    {
        return StockIn::whereHas('user.role', fn ($q) => $q->where('slug', Role::ADMIN));
    }

    /**
     * Query dasar "Barang Keluar" untuk grafik/statistik dashboard.
     *
     * Aturan bisnis: hanya dihitung saat barang dikirim KELUAR DARI GUDANG
     * UTAMA menuju divisi lain (Gudang Resto, Kasir, Kitchen). Secara teknis
     * ini adalah StockOut dengan location = 'gudang', yang HANYA tercipta
     * lewat Admin\OrderController::approve() saat admin menyetujui
     * permintaan Kasir/Kitchen.
     *
     * StockOut dengan location = 'restoran' (dicatat sendiri oleh Kasir/
     * Kitchen saat mereka memakai/menjual stok) SENGAJA TIDAK dihitung,
     * karena itu bukan "barang keluar dari Gudang Utama".
     */
    private function baseKeluarQuery()
    {
        return StockOut::where('location', StockOut::LOCATION_GUDANG_UTAMA);
    }

    /**
     * Query dasar "Permintaan" untuk grafik/statistik dashboard.
     *
     * Aturan bisnis: hanya permintaan yang dibuat oleh role Kasir atau
     * Kitchen yang dihitung (permintaan restock ke Gudang Utama).
     */
    private function basePermintaanQuery()
    {
        return Order::whereHas('user.role', fn ($q) => $q->whereIn('slug', [Role::KASIR, Role::KITCHEN]));
    }

    /**
     * Bangun ekspresi SQL untuk mengambil bagian tanggal (tahun/bulan/hari/jam)
     * dari sebuah kolom, secara PORTABLE antar driver database.
     *
     * PENTING: fungsi seperti MONTH(), YEAR(), HOUR() itu MySQL-only dan
     * TIDAK ADA di SQLite (driver default project ini — lihat DB_CONNECTION
     * di .env.example). Kalau project jalan di atas SQLite (biasanya dipakai
     * saat development lokal / php artisan serve), query lama akan gagal
     * total ("no such function: MONTH") dan fetch() grafik akan error diam-
     * diam di background.
     */
    private function datePart(string $column, string $part): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return match ($part) {
                'year'  => "CAST(strftime('%Y', {$column}) AS INTEGER)",
                'month' => "CAST(strftime('%m', {$column}) AS INTEGER)",
                'day'   => "CAST(strftime('%d', {$column}) AS INTEGER)",
                'hour'  => "CAST(strftime('%H', {$column}) AS INTEGER)",
            };
        }

        // MySQL / MariaDB (target production umum di hosting cPanel)
        return match ($part) {
            'year'  => "YEAR({$column})",
            'month' => "MONTH({$column})",
            'day'   => "DAYOFMONTH({$column})",
            'hour'  => "HOUR({$column})",
        };
    }

    public function admin(): View
    {
        $user = Auth::user();

        $permintaanMenunggu  = Order::where('status', Order::STATUS_MENUNGGU)->count();
        $permintaanDisetujui = Order::where('status', Order::STATUS_DISETUJUI)->count();
        $permintaanDitolak   = Order::where('status', Order::STATUS_DITOLAK)->count();

        $totalBarang   = Item::count();
        $totalUser     = User::count();
        $totalSupplier = Supplier::count();

        $tahunMulaiGrafik = self::TAHUN_MULAI_GRAFIK;

        $masukHariIni  = (int) $this->baseMasukQuery()->whereDate('tanggal', today())->sum('quantity');
        $keluarHariIni = (int) $this->baseKeluarQuery()->whereDate('tanggal', today())->sum('quantity');

        // Barang stok rendah = saldo divisi <= 10 untuk ke 4 divisi
        $stokRendah = Item::get()
            ->filter(fn ($item) => $item->stokByLocation($item->master_location) <= 10)
            ->count();

        // Gabungkan order dari semua role (kasir + kitchen + admin), latest 10
        $ordersRecent = Order::with(['item', 'user', 'user.role'])
            ->latest()
            ->limit(10)
            ->get();

        $itemsPerDivision = Item::selectRaw('master_location, count(*) as total')
            ->groupBy('master_location')
            ->pluck('total', 'master_location');

        $divisionMap = [
            Item::MASTER_GUDANG_UTAMA => 'Gudang Utama',
            Item::MASTER_GUDANG_RESTO => 'Gudang Resto',
            Item::MASTER_KASIR        => 'Kasir',
            Item::MASTER_KITCHEN      => 'Kitchen',
        ];

        $donutLabels = [];
        $donutData   = [];
        foreach ($divisionMap as $key => $label) {
            $donutLabels[] = $label;
            $donutData[]   = $itemsPerDivision[$key] ?? 0;
        }

        return view('dashboard.admin', compact(
            'user', 'permintaanMenunggu', 'permintaanDisetujui', 'permintaanDitolak',
            'totalBarang', 'totalUser', 'totalSupplier', 'masukHariIni', 'keluarHariIni',
            'stokRendah', 'ordersRecent', 'tahunMulaiGrafik', 'donutLabels', 'donutData'
        ));
    }

    /**
     * Endpoint AJAX: data grafik bulanan.
     *
     * - Kalau parameter `month` DIKIRIM (tab "Bulan" di UI kirim month & year):
     *   balikin breakdown PER MINGGU (tepat 4 minggu) untuk bulan itu saja.
     * - Kalau `month` TIDAK dikirim: balikin breakdown per-bulan untuk 1 tahun
     *   penuh (perilaku lama, dipertahankan untuk kompatibilitas).
     */
    public function chartData(Request $request): JsonResponse
    {
        $year  = (int) $request->query('year', now()->year);
        $month = $request->query('month');

        if ($month) {
            return $this->chartDataPerMinggu($year, (int) $month);
        }

        $bulanLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $bulanExpr        = $this->datePart('tanggal', 'month');
        $bulanExprCreated = $this->datePart('created_at', 'month');

        $masukPerBulan = $this->baseMasukQuery()
            ->whereYear('tanggal', $year)
            ->selectRaw("{$bulanExpr} as bulan, SUM(quantity) as total")
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $keluarPerBulan = $this->baseKeluarQuery()
            ->whereYear('tanggal', $year)
            ->selectRaw("{$bulanExpr} as bulan, SUM(quantity) as total")
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $permintaanPerBulan = $this->basePermintaanQuery()
            ->whereYear('created_at', $year)
            ->selectRaw("{$bulanExprCreated} as bulan, COUNT(*) as total")
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

    /**
     * Breakdown PER MINGGU untuk satu bulan tertentu — SELALU TEPAT 4 MINGGU
     * (dipanggil dari chartData saat tab "Bulan" memilih bulan+tahun spesifik).
     */
    private function chartDataPerMinggu(int $year, int $month): JsonResponse
    {
        $hariExpr        = $this->datePart('tanggal', 'day');
        $hariExprCreated = $this->datePart('created_at', 'day');

        $masukPerHari = $this->baseMasukQuery()
            ->whereYear('tanggal', $year)->whereMonth('tanggal', $month)
            ->selectRaw("{$hariExpr} as hari, SUM(quantity) as total")
            ->groupBy('hari')
            ->pluck('total', 'hari');

        $keluarPerHari = $this->baseKeluarQuery()
            ->whereYear('tanggal', $year)->whereMonth('tanggal', $month)
            ->selectRaw("{$hariExpr} as hari, SUM(quantity) as total")
            ->groupBy('hari')
            ->pluck('total', 'hari');

        $permintaanPerHari = $this->basePermintaanQuery()
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->selectRaw("{$hariExprCreated} as hari, COUNT(*) as total")
            ->groupBy('hari')
            ->pluck('total', 'hari');

        $jumlahHari = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Selalu dibagi TEPAT 4 minggu (bukan 5) — Minggu 4 menampung semua
        // sisa hari di akhir bulan (termasuk tanggal 29/30/31 kalau ada),
        // supaya bulan dengan 29-31 hari tidak memunculkan "Minggu 5".
        $batasMinggu = [
            1 => [1, 7],
            2 => [8, 14],
            3 => [15, 21],
            4 => [22, $jumlahHari],
        ];

        $labels = $masuk = $keluar = $permintaan = [];

        foreach ($batasMinggu as $minggu => [$awalHari, $akhirHari]) {
            $labels[] = 'Minggu ' . $minggu;

            $totalMasuk = $totalKeluar = $totalPermintaan = 0;
            for ($hari = $awalHari; $hari <= $akhirHari; $hari++) {
                $totalMasuk      += (int) ($masukPerHari[$hari] ?? 0);
                $totalKeluar     += (int) ($keluarPerHari[$hari] ?? 0);
                $totalPermintaan += (int) ($permintaanPerHari[$hari] ?? 0);
            }

            $masuk[]      = $totalMasuk;
            $keluar[]     = $totalKeluar;
            $permintaan[] = $totalPermintaan;
        }

        return response()->json([
            'labels'     => $labels,
            'masuk'      => $masuk,
            'keluar'     => $keluar,
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Endpoint AJAX: data grafik harian (per jam, hari ini).
     * Mengembalikan data per jam (00-23) untuk tanggal hari ini.
     */
    public function chartDataHarian(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $jamExpr = $this->datePart('created_at', 'hour');

        $masukPerJam = $this->baseMasukQuery()
            ->whereDate('tanggal', $date)
            ->selectRaw("{$jamExpr} as jam, SUM(quantity) as total")
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $keluarPerJam = $this->baseKeluarQuery()
            ->whereDate('tanggal', $date)
            ->selectRaw("{$jamExpr} as jam, SUM(quantity) as total")
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $permintaanPerJam = $this->basePermintaanQuery()
            ->whereDate('created_at', $date)
            ->selectRaw("{$jamExpr} as jam, COUNT(*) as total")
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $masuk = $keluar = $permintaan = $labels = [];

        for ($jam = 0; $jam <= 23; $jam++) {
            $labels[]     = sprintf('%02d:00', $jam);
            $masuk[]      = (int) ($masukPerJam[$jam] ?? 0);
            $keluar[]     = (int) ($keluarPerJam[$jam] ?? 0);
            $permintaan[] = (int) ($permintaanPerJam[$jam] ?? 0);
        }

        return response()->json([
            'labels'     => $labels,
            'masuk'      => $masuk,
            'keluar'     => $keluar,
            'permintaan' => $permintaan,
            'date'       => $date,
        ]);
    }

    /**
     * Endpoint AJAX: data grafik tahunan — mulai dari TAHUN_MULAI_GRAFIK
     * sampai tahun berjalan, otomatis bertambah tiap tahun baru.
     */
    public function chartDataTahunan(Request $request): JsonResponse
    {
        $currentYear = now()->year;
        $startYear   = min(self::TAHUN_MULAI_GRAFIK, $currentYear); // jaga-jaga kalau jam server keliru

        $tahunExpr        = $this->datePart('tanggal', 'year');
        $tahunExprCreated = $this->datePart('created_at', 'year');

        $masukPerTahun = $this->baseMasukQuery()
            ->whereBetween(DB::raw($tahunExpr), [$startYear, $currentYear])
            ->selectRaw("{$tahunExpr} as tahun, SUM(quantity) as total")
            ->groupBy('tahun')
            ->pluck('total', 'tahun');

        $keluarPerTahun = $this->baseKeluarQuery()
            ->whereBetween(DB::raw($tahunExpr), [$startYear, $currentYear])
            ->selectRaw("{$tahunExpr} as tahun, SUM(quantity) as total")
            ->groupBy('tahun')
            ->pluck('total', 'tahun');

        $permintaanPerTahun = $this->basePermintaanQuery()
            ->whereBetween(DB::raw($tahunExprCreated), [$startYear, $currentYear])
            ->selectRaw("{$tahunExprCreated} as tahun, COUNT(*) as total")
            ->groupBy('tahun')
            ->pluck('total', 'tahun');

        $masuk = $keluar = $permintaan = $labels = [];

        for ($tahun = $startYear; $tahun <= $currentYear; $tahun++) {
            $labels[]     = (string) $tahun;
            $masuk[]      = (int) ($masukPerTahun[$tahun] ?? 0);
            $keluar[]     = (int) ($keluarPerTahun[$tahun] ?? 0);
            $permintaan[] = (int) ($permintaanPerTahun[$tahun] ?? 0);
        }

        return response()->json([
            'labels'     => $labels,
            'masuk'      => $masuk,
            'keluar'     => $keluar,
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Endpoint AJAX: data grafik stok barang per divisi.
     * Mengembalikan label nama barang dan stok per lokasi (master_location).
     */
    public function divisionStockData(Request $request): JsonResponse
    {
        $division = $request->query('division', 'gudang_utama');

        $items = Item::where('master_location', $division)->get();

        $labels    = [];
        $stockData = [];

        foreach ($items as $item) {
            $labels[]    = $item->name;
            $stockData[] = $item->totalStock();
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $stockData,
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

        // Barang stok rendah di Kasir (berdasarkan master_location)
        $stokRendah = Item::where('master_location', Item::MASTER_KASIR)
            ->get()
            ->filter(fn ($item) => $item->stokKasir() <= 10)
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

        // Barang stok rendah di Kitchen (berdasarkan master_location)
        $stokRendah = Item::where('master_location', Item::MASTER_KITCHEN)
            ->get()
            ->filter(fn ($item) => $item->stokKitchen() <= 10)
            ->count();

        $ordersRecent = Order::with('item')
            ->where('user_id', $userId)
            ->latest()->limit(5)->get();

        return view('dashboard.kitchen', compact(
            'user', 'permintaanMenunggu', 'keluarHariIni', 'stokRendah', 'ordersRecent'
        ));
    }
}
