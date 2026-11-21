<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'store_name',
        'campus_hub',
        'address',
        'description',
        'contact',
        'opening_time',
        'closing_time',
        'store_status',
        'crowd_status',
        'is_live',
        'avatar',
        'is_verified',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the store.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menus for the store.
     *
     * @return HasMany
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get the orders for the store.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the visits for the store.
     *
     * @return HasMany
     */
    public function visits(): HasMany
    {
        return $this->hasMany(StoreVisit::class);
    }

    /**
     * Determine if store details are complete.
     *
     * @return bool
     */
    public function isDetailsComplete(): bool
    {
        return !empty($this->store_name)
            && !empty($this->address)
            && !empty($this->contact)
            && !empty($this->opening_time)
            && !empty($this->closing_time);
    }

    /**
     * Determine if store has available active menus.
     *
     * @return bool
     */
    public function hasActiveMenus(): bool
    {
        return $this->menus()->where('availability_status', true)->count() > 0;
    }

    /**
     * Determine if the store can go live.
     *
     * @return array{can_go_live: bool, reasons: array<int, string>}
     */
    public function validateGoLiveRequirements(): array
    {
        $reasons = [];

        if (!$this->isDetailsComplete()) {
            $reasons[] = 'Informasi toko (nama, alamat, kontak, jam operasional) harus dilengkapi terlebih dahulu di menu Pengaturan.';
        }

        if (!$this->hasActiveMenus()) {
            $reasons[] = 'Toko harus memiliki setidaknya satu menu aktif yang berstatus tersedia.';
        }

        return [
            'can_go_live' => count($reasons) === 0,
            'reasons' => $reasons,
        ];
    }
}

