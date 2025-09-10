<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;

class Car_detail extends Model
{
    protected $fillable = [
        'body_type',
        'condition',
        'year',
        'cylinders',
        'mileage',
        'transmission',
        'displacement',
        'color',
        'fuel_type',
        'drive_type',
        'doors',
        'vin',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
    
}
