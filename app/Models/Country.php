<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected  $fillable = [
        'iso',
        'name',
        'nicename',
        'iso3',
        'numcode',
        'phonecode',
    ];

    protected $casts = [
        'iso'      => 'string',
        'name'     => 'string',
        'nicename' => 'string',
        'iso3'     => 'string',
        'numcode'  => 'integer',
        'phonecode'=> 'integer',
    ];
}
