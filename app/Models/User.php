<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MERCHANT = 'merchant';

    public const ROLE_BUYER = 'buyer';

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'username',
        'name',
        'password',
        'role',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
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
     * Compatibility accessor for Laravel Breeze (`$user->id`).
     */
    public function getIdAttribute(): ?int
    {
        return $this->user_id;
    }

    /**
     * Compatibility accessor for Laravel Breeze (`Auth::user()->name`).
     */
    public function getNameAttribute(): ?string
    {
        return $this->username;
    }

    /**
     * Compatibility mutator for Laravel Breeze registration & profile update.
     */
    public function setNameAttribute(?string $value): void
    {
        $this->attributes['username'] = $value;
    }

    public function isAdmin(): bool
    {
        return strtolower((string) $this->role) === self::ROLE_ADMIN;
    }

    public function isMerchant(): bool
    {
        return strtolower((string) $this->role) === self::ROLE_MERCHANT;
    }

    public function isBuyer(): bool
    {
        return strtolower((string) $this->role) === self::ROLE_BUYER;
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'user_id', 'user_id');
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'user_id', 'user_id');
    }
}
