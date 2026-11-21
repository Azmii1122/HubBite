<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function getNameAttribute()
    {
        return $this->username;
    }

    public function merchants()
    {
        return $this->hasMany(Merchant::class, 'user_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id', 'user_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'buyer_id', 'user_id');
    }
}
