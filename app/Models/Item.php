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
