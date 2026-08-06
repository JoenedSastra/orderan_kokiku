<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * Slug konstanta supaya tidak ada "magic string" tersebar di codebase.
     */
    public const ADMIN = 'admin';
    public const KASIR = 'kasir';
    public const KITCHEN = 'kitchen';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Semua user yang memiliki role ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
