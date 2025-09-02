<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // Fillable
    protected $fillable = [
        'owner_id',
        'customer_id',
        'description',
        'price',
    ];
}
