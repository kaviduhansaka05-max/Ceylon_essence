<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = ['code','type','amount','min','expires_at','active'];

    protected $casts = [
        'amount'     => 'float',
        'min'        => 'float',
        'active'     => 'boolean',
        'expires_at' => 'datetime',
    ];
}
