<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Car extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'model',
        'color',
        'year',
        'price',
        'description',
        'image',
        'favorite',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ One car has one detail
    public function car_detail()
    {
        return $this->hasOne(Car_detail::class);
    }

    // ✅ One car can have many images
    public function multi_images()
    {
        return $this->hasMany(Multi_image::class);
    }

    // ✅ One car has one internal characteristic
    public function char_internal()
    {
        return $this->hasOne(Char_internal::class);
    }

    // ✅ One car has one external characteristic
    public function char_external()
    {
        return $this->hasOne(Char_external::class);
    }

    // ✅ One car has one comfort characteristic
    public function char_comfort()
    {
        return $this->hasOne(Char_comfort::class);
    }

    // ✅ One car has one safety characteristic
    public function char_sefety()
    {
        return $this->hasOne(Char_sefety::class);
    }

    public function quick_spec()
    {
        return $this->hasMany(Quick_spec::class, 'car_id', 'id');
    }


}
