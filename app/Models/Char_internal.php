<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Char_internal extends Model
{
    protected $fillable=[
        'car_id',
        'name',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }


}
