<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
     protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
    ];

     public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'OrderID');
    }

    // Each order item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'Product_ID');
    }   
}
