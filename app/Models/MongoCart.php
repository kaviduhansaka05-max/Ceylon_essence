<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\EmbedsMany;

class MongoCart extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'carts';

   // ...
protected $fillable = [
    'user_id','status','items','total','quantity',
    'promo_code','promo_type','promo_amount','promo_min','promo_expires_at',
    'discount','grand_total',
];

protected $casts = [
    'total'       => 'float',
    'quantity'    => 'integer',
    'discount'    => 'float',
    'grand_total' => 'float',
    'promo_amount'=> 'float',
    'promo_min'   => 'float',
    'promo_expires_at' => 'datetime',
];


    protected $attributes = [
        'status'   => 'open',
        'items'    => [],   // keep this default
        'total'    => 0,
        'quantity' => 0,
    ];

    // ⛔️ REMOVE this:
    // protected $casts = [ 'items' => 'array' ];

    public function items(): \MongoDB\Laravel\Relations\EmbedsMany
    {
        return $this->embedsMany(MongoCartItem::class);
    }

    public function recomputeTotals(): void
    {
        $qty = 0; $sum = 0.0;
        foreach ($this->items as $it) { $qty += (int)$it->quantity; $sum += (float)$it->total; }
        $this->quantity = $qty;
        $this->total    = round($sum, 2);
    }
}
