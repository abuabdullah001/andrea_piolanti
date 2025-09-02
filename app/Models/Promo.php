<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promo_code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Relation with MenuItem Model
     * A Promo belongs to one MenuItem
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
