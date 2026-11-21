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

    public const STATUS_NEW = 'new';

    public const STATUS_COOKING = 'cooking';

    public const STATUS_READY = 'ready';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'orders';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_code',
        'order_number',
        'user_id',
        'store_id',
        'customer_name',
        'delivery_address',
        'customer_location',
        'service_type',
        'delivery_type',
        'delivery_fee',
        'table_number',
        'special_notes',
        'notes',
        'payment_method',
        'payment_status',
        'order_status',
        'subtotal',
        'total',
        'qris_code',
        'is_priority',
        'decline_reason',
        'preparation_time_minutes',
        'received_at',
        'cooking_at',
        'ready_at',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
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

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'store_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    public function getOrderNumberAttribute(?string $value): string
    {
        return $value ?? $this->order_code ?? ('HB-'.$this->id);
    }

    public function getNotesAttribute(): ?string
    {
        return $this->special_notes;
    }

    public function setNotesAttribute(?string $value): void
    {
        $this->attributes['special_notes'] = $value;
    }

    public function getStatusAttribute(): string
    {
        return $this->order_status ?? self::STATUS_NEW;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format((float) $this->total, 0, ',', '.');
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_NEW => 'Menunggu / Baru',
            self::STATUS_COOKING => 'Sedang Dimasak',
            self::STATUS_READY => 'Siap Disajikan / Diantar',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_DECLINED => 'Ditolak',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

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
