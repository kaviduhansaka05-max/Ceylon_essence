<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    protected $fillable = [
        'customer_id',
        'total_price',
    ];

    // A shopping cart belongs to a customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'CustomerID');
    }

    // A shopping cart has many cart items
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'CartID');
    }

    // Through cart items, a shopping cart can have many products
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'cart_item',     // Pivot table
            'cart_id',       // Foreign key on pivot table for this model
            'product_id',    // Foreign key on pivot table for related model
            'CartID',        // Local key on this model
            'Product_ID'     // Local key on related model
        );
    }
}
