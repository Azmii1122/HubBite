<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * Allowed order statuses.
     */
    public const STATUS_NEW = 'new';
    public const STATUS_COOKING = 'cooking';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'order_number',
        'customer_name',
        'customer_location',
        'service_type',
        'order_status',
        'payment_method',
        'subtotal',
        'total',
        'special_notes',
        'is_priority',
        'decline_reason',
        'table_number',
        'preparation_time_minutes',
        'received_at',
        'cooking_at',
        'ready_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'is_priority' => 'boolean',
            'preparation_time_minutes' => 'integer',
            'received_at' => 'datetime',
            'cooking_at' => 'datetime',
            'ready_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the order.
     *
     * @return BelongsTo
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the items for the order.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment for the order.
     *
     * @return HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Format the total price in Indonesian Rupiah.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /**
     * Determine whether the order can transition to the given status.
     *
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $current = $this->order_status;

        $validTransitions = [
            self::STATUS_NEW => [self::STATUS_COOKING, self::STATUS_DECLINED, self::STATUS_CANCELLED],
            self::STATUS_COOKING => [self::STATUS_READY, self::STATUS_CANCELLED],
            self::STATUS_READY => [self::STATUS_COMPLETED],
            self::STATUS_COMPLETED => [],
            self::STATUS_DECLINED => [],
            self::STATUS_CANCELLED => [],
        ];

        return in_array($newStatus, $validTransitions[$current] ?? [], true);
    }
}

