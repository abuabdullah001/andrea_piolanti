<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable = [
        'name',
        'country',
        'city',
        'post',
        'email',
        'phone',
        'announce',
        'message'
    ];
}
