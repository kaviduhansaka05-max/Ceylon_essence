<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
     protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
    ];

    // A customer can have many orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'CustomerID', 'CustomerID');
    }

    // A customer can have many order items through orders
    public function orderItems()
    {
        return $this->hasManyThrough(
            OrderItem::class,  // Final related model
            Order::class,      // Intermediate model
            'CustomerID',      // Foreign key on orders table
            'OrderID',         // Foreign key on order_items table
            'CustomerID',      // Local key on customers table
            'OrderID'          // Local key on orders table
        );
    }
}
