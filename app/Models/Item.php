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
     * Hitung stok saat ini untuk user tertentu (berdasarkan role/lokasi).
     * Stok = total masuk - total keluar dari user dengan role yang sama.
     */
    public function currentStockForRole(string $roleSlug): int
    {
        $masuk = $this->stockIns()
            ->whereHas('user.role', fn ($q) => $q->where('slug', $roleSlug))
            ->sum('quantity');

        $keluar = $this->stockOuts()
            ->whereHas('user.role', fn ($q) => $q->where('slug', $roleSlug))
            ->sum('quantity');

        return max(0, $masuk - $keluar);
    }
}
