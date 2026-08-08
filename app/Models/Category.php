<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public const USED_BY_KASIR   = 'kasir';
    public const USED_BY_KITCHEN = 'kitchen';
    public const USED_BY_UMUM    = 'umum';

    protected $fillable = ['name', 'used_by'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
