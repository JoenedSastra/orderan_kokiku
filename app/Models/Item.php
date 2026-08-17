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

    public function stokGudangUtama(): int
    {
        $masuk  = $this->stockIns()->where('location', StockIn::LOCATION_GUDANG_UTAMA)->sum('quantity');
        $keluar = $this->stockOuts()->where('location', StockOut::LOCATION_GUDANG_UTAMA)->sum('quantity');
        return max(0, $masuk - $keluar);
    }

    public function stokKasir(): int
    {
        $masuk  = $this->stockIns()->where('location', StockIn::LOCATION_KASIR)->sum('quantity');
        $keluar = $this->stockOuts()->where('location', StockOut::LOCATION_KASIR)->sum('quantity');
        return max(0, $masuk - $keluar);
    }

    public function stokKitchen(): int
    {
        $masuk  = $this->stockIns()->where('location', StockIn::LOCATION_KITCHEN)->sum('quantity');
        $keluar = $this->stockOuts()->where('location', StockOut::LOCATION_KITCHEN)->sum('quantity');
        return max(0, $masuk - $keluar);
    }

    public function stokByLocation(string $locationKey): int
    {
        return match ($locationKey) {
            'gudang_utama', 'gudang_resto', 'gudang'  => $this->stokGudangUtama(),
            'kasir'   => $this->stokKasir(),
            'kitchen' => $this->stokKitchen(),
            default   => 0,
        };
    }

    public function masukByLocation(string $locationKey): int
    {
        $location = match ($locationKey) {
            'gudang_utama', 'gudang_resto', 'gudang' => StockIn::LOCATION_GUDANG_UTAMA,
            'kasir'   => StockIn::LOCATION_KASIR,
            'kitchen' => StockIn::LOCATION_KITCHEN,
            default   => StockIn::LOCATION_GUDANG_UTAMA,
        };
        
        // Untuk SEMUA divisi, kolom Masuk menghitung semua barang masuk
        // termasuk penyesuaian stok manual yang menambah stok
        return $this->stockIns()
                    ->where('location', $location)
                    ->sum('quantity');
    }

    public function keluarByLocation(string $locationKey): int
    {
        $location = match ($locationKey) {
            'gudang_utama', 'gudang_resto', 'gudang' => StockOut::LOCATION_GUDANG_UTAMA,
            'kasir'   => StockOut::LOCATION_KASIR,
            'kitchen' => StockOut::LOCATION_KITCHEN,
            default   => StockOut::LOCATION_GUDANG_UTAMA,
        };
        
        // Untuk SEMUA divisi, kolom Keluar menghitung semua barang keluar
        // termasuk pengiriman barang maupun penyesuaian stok manual yang mengurangi stok
        // sehingga Masuk - Keluar = Sisa akan selalu seimbang.
        return $this->stockOuts()
                    ->where('location', $location)
                    ->sum('quantity');
    }

    public function totalStock(): int
    {
        return $this->stokGudangUtama() + $this->stokKasir() + $this->stokKitchen();
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

    public function latestActivity(): StockIn|StockOut|null
    {
        $location = match ($this->master_location) {
            self::MASTER_GUDANG_UTAMA => StockIn::LOCATION_GUDANG_UTAMA,
            self::MASTER_KASIR        => StockIn::LOCATION_KASIR,
            self::MASTER_KITCHEN      => StockIn::LOCATION_KITCHEN,
            default                   => StockIn::LOCATION_GUDANG_UTAMA,
        };

        $masuk  = $this->stockIns()->where('location', $location)->latest('tanggal')->latest('created_at')->first();
        $keluar = $this->stockOuts()->where('location', $location)->latest('tanggal')->latest('created_at')->first();

        return collect([$masuk, $keluar])
            ->filter()
            ->sortByDesc(fn ($event) => $event->created_at)
            ->first();
    }

    public function latestMasukActivity(): ?StockIn
    {
        $location = match ($this->master_location) {
            self::MASTER_GUDANG_UTAMA => StockIn::LOCATION_GUDANG_UTAMA,
            self::MASTER_KASIR        => StockIn::LOCATION_KASIR,
            self::MASTER_KITCHEN      => StockIn::LOCATION_KITCHEN,
            default                   => StockIn::LOCATION_GUDANG_UTAMA,
        };

        return $this->stockIns()->where('location', $location)->latest('tanggal')->latest('created_at')->first();
    }

    public function latestKeluarActivity(): ?StockOut
    {
        $location = match ($this->master_location) {
            self::MASTER_GUDANG_UTAMA => StockOut::LOCATION_GUDANG_UTAMA,
            self::MASTER_KASIR        => StockOut::LOCATION_KASIR,
            self::MASTER_KITCHEN      => StockOut::LOCATION_KITCHEN,
            default                   => StockOut::LOCATION_GUDANG_UTAMA,
        };

        return $this->stockOuts()->where('location', $location)->latest('tanggal')->latest('created_at')->first();
    }

    public function currentStockForRole(string $roleSlug): int
    {
        $location = match ($roleSlug) {
            Role::ADMIN   => StockIn::LOCATION_GUDANG_UTAMA,
            Role::KASIR   => StockIn::LOCATION_KASIR,
            Role::KITCHEN => StockIn::LOCATION_KITCHEN,
            default       => StockIn::LOCATION_GUDANG_UTAMA,
        };

        $masuk  = $this->stockIns()->where('location', $location)->sum('quantity');
        $keluar = $this->stockOuts()->where('location', $location)->sum('quantity');
        return max(0, $masuk - $keluar);
    }
}
