<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOut extends Model
{
    use HasFactory;

    public const LOCATION_GUDANG_UTAMA = 'gudang_utama';
    public const LOCATION_KASIR        = 'kasir';
    public const LOCATION_KITCHEN      = 'kitchen';

    protected $fillable = [
        'item_id', 'user_id',
        'quantity', 'keterangan', 'tanggal', 'location',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
