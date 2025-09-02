<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    // Fillable
    protected $fillable = [
        'booking_id',
        'item_id',
        'description',
        'price'
    ];
}
