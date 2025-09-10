<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Card extends Model
{


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function car_detail()
    {
        return $this->hasOne(Car_detail::class);
    }
}
