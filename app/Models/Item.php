<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'unit', 'min_stock', 'description'];

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
     * Stok Kasir = stok Restoran, hanya dihitung jika kategori barang ini
     * "milik" Kasir atau Umum (dipakai bersama).
     */
    public function stokKasir(): int
    {
        $usedBy = $this->category?->used_by;

        if ($usedBy === Category::USED_BY_KASIR || $usedBy === Category::USED_BY_UMUM) {
            return $this->stokRestoran();
        }

        return 0;
    }

    /**
     * Stok Kitchen = stok Restoran, hanya dihitung jika kategori barang ini
     * "milik" Kitchen atau Umum (dipakai bersama).
     */
    public function stokKitchen(): int
    {
        $usedBy = $this->category?->used_by;

        if ($usedBy === Category::USED_BY_KITCHEN || $usedBy === Category::USED_BY_UMUM) {
            return $this->stokRestoran();
        }

        return 0;
    }

    /**
     * Ambil nilai stok sesuai kunci lokasi ('gudang', 'resto', 'kasir', 'kitchen').
     * Dipakai supaya tampilan Stock Barang bisa generik per-tab lokasi.
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
