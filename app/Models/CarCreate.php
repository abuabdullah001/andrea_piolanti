<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Car;
use App\Models\Car_detail;
use App\Models\Multi_image;
use App\Models\Char_internal;
use App\Models\Char_external;
use App\Models\Char_comfort;
use App\Models\Char_sefety;


class CarCreate extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'name',
        'model',
        'brand_name',
        'description',
        'image',
        'multiple_image',
        'location',
        'date',
        'price',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function car_detail()
    {
        return $this->belongsTo(Car_Detail::class);
    }


    public function multi_image()
    {
        return $this->belongsTo(Multi_image::class);
    }

    public function char_internal()
    {
        return $this->belongsTo(Char_internal::class);
    }

    public function char_external()
    {
        return $this->belongsTo(Char_external::class);
    }

    public function char_comfort()
    {
        return $this->belongsTo(Char_comfort::class);
    }

    public function char_sefety()
    {
        return $this->belongsTo(Char_sefety::class);
    }

}
