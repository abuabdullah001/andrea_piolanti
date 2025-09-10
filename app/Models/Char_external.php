<?php

namespace App\Models;

use App\Models\Car;
use Illuminate\Database\Eloquent\Model;

class Char_external extends Model
{
    protected $fillable = [
        'name',
        'car_id',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
