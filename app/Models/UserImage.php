<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserImage extends Model
{
    // Fillable
    protected $fillable = [
        'user_id',
        'image',
        'serial',
    ];
    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
