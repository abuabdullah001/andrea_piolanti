<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;

class Char_sefety extends Model
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
