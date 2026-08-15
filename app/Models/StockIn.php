<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIn extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    public const LOCATION_GUDANG_UTAMA = 'gudang_utama';
    public const LOCATION_KASIR        = 'kasir';
    public const LOCATION_KITCHEN      = 'kitchen';

    protected $fillable = [
        'item_id', 'user_id', 'supplier_id',
        'quantity', 'keterangan', 'is_completed', 'tanggal', 'location',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'is_completed' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
