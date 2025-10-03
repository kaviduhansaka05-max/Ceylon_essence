<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password', 'user_type'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+ automatically hashes
    ];

     // An admin can create many products
    public function products()
    {
        return $this->hasMany(Product::class, 'created_by'); 
        //  products table has a created_by column (admin_id)
    }

    // An admin can manage many orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'managed_by');
        //  orders table has a managed_by (admin_id)
    }

    // An admin can issue many promos
    public function promos()
    {
        return $this->hasMany(Promo::class, 'admin_id');
    }
}
