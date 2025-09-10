<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;

class Quick_spec extends Model
{
    protected $fillable = [
            'category',
            'transmission',
            'miles',
            'fuelLiter',
            'car_id'
    ];

public function car()
{
    return $this->belongsTo(Car::class, 'car_id', 'id');
}


    
}
