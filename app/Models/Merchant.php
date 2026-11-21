<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $table = 'merchants';
    protected $primaryKey = 'merchant_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_email',
        'store_name',
        'is_open',
        'live_crowd_status',
        'allow_delivery',
        'wallet_balance',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'allow_delivery' => 'boolean',
        'wallet_balance' => 'decimal:2',
    ];

    // Compatibility accessors untuk UI Web
    public function getNameAttribute()
    {
        return $this->store_name;
    }

    public function getSlugAttribute()
    {
        return $this->merchant_id;
    }

    public function getImageAttribute()
    {
        return 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80';
    }

    public function getBannerImageAttribute()
    {
        return 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80';
    }

    public function getRatingAttribute()
    {
        return 4.9;
    }

    public function getRatingCountAttribute()
    {
        return '1.2K+ Rating';
    }

    public function getDistanceAttribute()
    {
        return '1.0 km';
    }

    public function getDeliveryTimeAttribute()
    {
        return '15-25 min';
    }

    public function getCategoryAttribute()
    {
        return 'Aneka Masakan Kampus';
    }

    public function getStatusAttribute()
    {
        return $this->is_open ? 'Buka' : 'Tutup';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'merchant_id', 'merchant_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'merchant_id', 'merchant_id');
    }
}
