<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    // Fillable fields
    protected $fillable = [
        'owner_id',
        'title',
        'slug',
        'description',
        'duration',
        'price',
        'is_deposite',
        'minimum_deposite',
        'tax',
        'service_at',
        'location',
        'image',
        'status',
    ];
    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function time_slots()
    {
        return $this->hasMany(ServiceTimeSlot::class);
    }
    public function unavailable_date_and_time_slots()
    {
        return $this->hasMany(ServiceUnavailableDateAndTimeSlot::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
