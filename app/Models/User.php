<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Role yang dimiliki user ini.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === Role::ADMIN;
    }

    public function isKasir(): bool
    {
        return $this->role?->slug === Role::KASIR;
    }

    public function isKitchen(): bool
    {
        return $this->role?->slug === Role::KITCHEN;
    }

    /**
     * Route dashboard sesuai role user, dipakai untuk redirect setelah login.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role?->slug) {
            Role::ADMIN => 'admin.dashboard',
            Role::KASIR => 'kasir.dashboard',
            Role::KITCHEN => 'kitchen.dashboard',
            default => 'login',
        };
    }
}
