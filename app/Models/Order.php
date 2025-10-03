<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'status',
        'location',
        'total',
        'quantity',
    ];

     // An order belongs to a customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'CustomerID');
    }

    // An order has many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'OrderID');
    }

       public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }
    // An order has many products through order items
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,   // Final related model
            OrderItem::class, // Intermediate model
            'order_id',       // Foreign key on order_items table
            'Product_ID',     // Foreign key on products table
            'OrderID',        // Local key on orders table
            'Product_ID'      // Local key on order_items table
        );
    }
}
