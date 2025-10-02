<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class MongoOrderItem extends Eloquent
{
    protected $connection = 'mongodb';

    /**
     * Set collection = null because this model
     * will be stored as an embedded document
     * inside MongoOrder (not its own collection).
     */
    protected $collection = null;

    protected $primaryKey = '_id';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'quantity',
        'image',
        'total',
    ];

    /**
     * Automatically cast fields to correct types
     */
    protected $casts = [
        'price'    => 'float',
        'quantity' => 'integer',
        'total'    => 'float',
    ];

    /**
     * Relationship back to the parent order
     */
    public function order()
    {
        return $this->belongsTo(MongoOrder::class, 'order_id');
    }
}
