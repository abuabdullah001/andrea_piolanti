<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'customer_id',
        'reviewable_id',
        'reviewable_type',
        'rating',
        'comment',
        'status'
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'customer_id');
    }

    // Likes
    public function likes()
    {
        return $this->belongsToMany(User::class, 'review_likes')->withTimestamps();
    }
}
