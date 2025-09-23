<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\EmbedsMany;

class MongoOrder extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'user_id',
        'status',     // 'pending' | 'paid' | ...
        'location',
        'total',
        'quantity',
        'items',
    ];

    protected $attributes = [
        'status'   => 'pending',
        'items'    => [],
        'total'    => 0,
        'quantity' => 0,
    ];

    public function items(): EmbedsMany
    {
        return $this->embedsMany(MongoOrderItem::class);
    }
}
