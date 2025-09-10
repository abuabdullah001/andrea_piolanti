<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;

class Char_comfort extends Model
{
    protected $fillable= [
        'name',
        'car_id',
    ];

    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }
}
