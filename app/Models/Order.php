<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

    protected $fillable = [
        'buyer_id',
        'buyer_email',
        'merchant_id',
        'order_mode',
        'payment_method',
        'status',
        'total_amount',
        'created_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Compatibility accessors
    public function getOrderCodeAttribute()
    {
        return 'HB-' . $this->order_id;
    }

    public function getTotalAttribute()
    {
        return $this->total_amount;
    }

    public function getOrderStatusAttribute()
    {
        return $this->status;
    }

    public function getDeliveryTypeAttribute()
    {
        return $this->order_mode ?? 'delivery';
    }

    public function getDeliveryFeeAttribute()
    {
        return 3000;
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'user_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
}
