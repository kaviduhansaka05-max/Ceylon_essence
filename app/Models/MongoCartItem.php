<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class MongoCartItem extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = null; // embedded subdocument

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'quantity',
        'image',
        'total',
        'status',
    ];

    protected $attributes = [
        'quantity' => 1,
    ];

    public function setQuantityAttribute($val)
    {
        $q = max(1, (int) $val);
        $this->attributes['quantity'] = $q;
        $p = (float) ($this->attributes['price'] ?? 0);
        $this->attributes['total'] = round($p * $q, 2);
    }

    public function setPriceAttribute($val)
    {
        $p = (float) $val;
        $this->attributes['price'] = $p;
        $q = (int) ($this->attributes['quantity'] ?? 1);
        $this->attributes['total'] = round($p * $q, 2);
    }
}
