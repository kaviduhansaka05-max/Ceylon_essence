<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];

    // A cart item belongs to a shopping cart
    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'cart_id', 'CartID');
    }

    // A cart item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'Product_ID');
    }
}
