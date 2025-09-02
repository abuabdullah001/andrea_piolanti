<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quick_spec extends Model
{
    protected $fillable = [
            
            'category',
            'transmission',
            'miles',
            'fuelLiter',
    ];
}
