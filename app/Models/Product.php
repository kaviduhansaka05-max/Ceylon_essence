<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'inventory',
        'price',
        'status',
        'sold_pieces',
    ];

    // A product can appear in many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'Product_ID');
    }

    // A product can belong to many orders through order items
    public function orders()
    {
        return $this->belongsToMany(
            Order::class,
            'order_item',     // Pivot table
            'product_id',     // Foreign key on pivot table for this model
            'order_id',       // Foreign key on pivot table for related model
            'Product_ID',     // Local key on this model
            'OrderID'         // Local key on related model
        );
    }
}
