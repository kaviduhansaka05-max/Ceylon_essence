<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class MongoOrderItem extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = null; // embedded

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'quantity',
        'image',
        'total',
    ];
}
