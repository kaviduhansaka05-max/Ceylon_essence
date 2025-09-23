<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class MongoProduct extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'products';
    protected $table      = 'products';

    protected $primaryKey = '_id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'category',
        'description',   // <-- added
        'size',          // <-- added
        'inventory',
        'price',
        'status',
        'sold_pieces',
        'image',
        'created_at',
        'updated_at',
    ];
}
