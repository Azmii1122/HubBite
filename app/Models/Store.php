<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'store_name',
        'slug',
        'campus_hub',
        'category',
        'description',
        'address',
        'contact',
        'opening_time',
        'closing_time',
        'store_status',
        'crowd_status',
        'is_live',
        'is_verified',
        'allow_delivery',
        'is_popular',
        'rating',
        'rating_count',
        'distance',
        'delivery_time',
        'avatar',
        'banner_image',
        'wallet_balance',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
            'is_verified' => 'boolean',
            'allow_delivery' => 'boolean',
            'is_popular' => 'boolean',
            'rating' => 'decimal:1',
            'wallet_balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'store_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'store_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'store_id', 'id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(StoreVisit::class, 'store_id', 'id');
    }

    // Compatibility accessors for Buyer/Customer UI
    public function getNameAttribute(): string
    {
        return $this->store_name;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->contact;
    }

    public function getStatusAttribute(): string
    {
        return $this->store_status === 'open' ? 'Buka' : 'Tutup';
    }

    public function getImageAttribute(): string
    {
        if (! empty($this->avatar) && str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        if (! empty($this->avatar) && file_exists(public_path($this->avatar))) {
            return asset($this->avatar);
        }

        return 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80';
    }

    public function getBannerImageAttribute(?string $value): string
    {
        if (! empty($value) && str_starts_with($value, 'http')) {
            return $value;
        }

        if (! empty($value) && file_exists(public_path($value))) {
            return asset($value);
        }

        return 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80';
    }

    public function isDetailsComplete(): bool
    {
        return ! empty($this->store_name)
            && ! empty($this->address)
            && ! empty($this->contact)
            && ! empty($this->opening_time)
            && ! empty($this->closing_time);
    }

    public function hasActiveMenus(): bool
    {
        return $this->menus()->where('availability_status', true)->count() > 0;
    }

    /**
     * @return array{can_go_live: bool, reasons: array<int, string>}
     */
    public function validateGoLiveRequirements(): array
    {
        $reasons = [];

        if (! $this->isDetailsComplete()) {
            $reasons[] = 'Informasi toko (nama, alamat, kontak, jam operasional) harus dilengkapi terlebih dahulu di menu Pengaturan.';
        }

        if (! $this->hasActiveMenus()) {
            $reasons[] = 'Toko harus memiliki setidaknya satu menu aktif yang berstatus tersedia.';
        }

        return [
            'can_go_live' => count($reasons) === 0,
            'reasons' => $reasons,
        ];
    }
}
