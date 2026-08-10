<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    public const MASTER_GUDANG_UTAMA = 'gudang_utama';
    public const MASTER_GUDANG_RESTO = 'gudang_resto';
    public const MASTER_KASIR        = 'kasir';
    public const MASTER_KITCHEN      = 'kitchen';

    protected $fillable = ['name', 'category_id', 'master_location', 'unit', 'min_stock', 'description'];

    protected $casts = [
        'min_stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Stok Gudang = masuk lokasi gudang - keluar lokasi gudang.
     */
    public function stokGudang(): int
    {
        $masuk  = $this->stockIns()->where('location', StockIn::LOCATION_GUDANG)->sum('quantity');
        $keluar = $this->stockOuts()->where('location', StockOut::LOCATION_GUDANG)->sum('quantity');
        return max(0, $masuk - $keluar);
    }

    /**
     * Stok Restoran = masuk lokasi restoran - keluar lokasi restoran.
     */
    public function stokRestoran(): int
    {
        $masuk  = $this->stockIns()->where('location', StockIn::LOCATION_RESTORAN)->sum('quantity');
        $keluar = $this->stockOuts()->where('location', StockOut::LOCATION_RESTORAN)->sum('quantity');
        return max(0, $masuk - $keluar);
    }

    /**
     * Stok Kasir = stok Restoran, hanya jika master_location barang ini = Kasir.
     */
    public function stokKasir(): int
    {
        return $this->master_location === self::MASTER_KASIR ? $this->stokRestoran() : 0;
    }

    /**
     * Stok Kitchen = stok Restoran, hanya jika master_location barang ini = Kitchen.
     */
    public function stokKitchen(): int
    {
        return $this->master_location === self::MASTER_KITCHEN ? $this->stokRestoran() : 0;
    }

    /**
     * Ambil nilai stok sesuai kunci lokasi ('gudang', 'resto', 'kasir', 'kitchen').
     */
    public function stokByLocation(string $locationKey): int
    {
        return match ($locationKey) {
            'gudang'  => $this->stokGudang(),
            'resto'   => $this->stokRestoran(),
            'kasir'   => $this->stokKasir(),
            'kitchen' => $this->stokKitchen(),
            default   => 0,
        };
    }

    /**
     * Total Stock yang relevan untuk master_location barang ini:
     * - Gudang Utama -> stok Gudang
     * - Gudang Resto / Kasir / Kitchen -> stok Restoran
     */
    public function totalStock(): int
    {
        return $this->master_location === self::MASTER_GUDANG_UTAMA
            ? $this->stokGudang()
            : $this->stokRestoran();
    }

    public function masterLocationLabel(): string
    {
        return match ($this->master_location) {
            self::MASTER_GUDANG_UTAMA => 'Gudang Utama',
            self::MASTER_GUDANG_RESTO => 'Gudang Resto',
            self::MASTER_KASIR        => 'Kasir',
            self::MASTER_KITCHEN      => 'Kitchen',
            default                   => '-',
        };
    }

    /**
     * Aktivitas stok TERAKHIR untuk barang ini, di ledger lokasi milik barang
     * ini sendiri (Gudang Utama -> ledger "gudang", Gudang Resto/Kasir/Kitchen
     * -> ledger "restoran"). Dipakai untuk kolom Hari/Tanggal/Jam & Keterangan
     * di Master Barang. Sengaja HANYA melihat ledger lokasi barang itu sendiri,
     * supaya keterangan Gudang Utama (mis. "Kirim barang ke Kasir") dan
     * keterangan barang tujuan (mis. "Diterima dari Gudang Utama") tetap
     * berdiri sendiri-sendiri dan tidak saling menimpa.
     */
    public function latestActivity(): StockIn|StockOut|null
    {
        $location = $this->master_location === self::MASTER_GUDANG_UTAMA
            ? StockIn::LOCATION_GUDANG
            : StockIn::LOCATION_RESTORAN;

        $masuk  = $this->stockIns()->where('location', $location)->latest('tanggal')->latest('created_at')->first();
        $keluar = $this->stockOuts()->where('location', $location)->latest('tanggal')->latest('created_at')->first();

        return collect([$masuk, $keluar])
            ->filter()
            ->sortByDesc(fn ($event) => $event->created_at)
            ->first();
    }

    /**
     * Hitung stok saat ini untuk user tertentu (berdasarkan role/lokasi).
     */
    public function currentStockForRole(string $roleSlug): int
    {
        $location = ($roleSlug === Role::ADMIN)
            ? StockIn::LOCATION_GUDANG
            : StockIn::LOCATION_RESTORAN;

        $masuk  = $this->stockIns()->where('location', $location)->sum('quantity');
        $keluar = $this->stockOuts()->where('location', $location)->sum('quantity');
        return max(0, $masuk - $keluar);
    }
}
