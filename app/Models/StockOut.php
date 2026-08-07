<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOut extends Model
{
    use HasFactory;

    public const LOCATION_GUDANG   = 'gudang';
    public const LOCATION_RESTORAN = 'restoran';

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
