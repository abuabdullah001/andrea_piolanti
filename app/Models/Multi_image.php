<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Multi_image extends Model
{
    protected $fillable = [
        'car_id',
        'images',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
