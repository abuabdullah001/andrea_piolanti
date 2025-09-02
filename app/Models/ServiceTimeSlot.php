<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTimeSlot extends Model
{
    // Fillable fields
    protected $fillable = [
        'service_id',
        'time',
        'status',
    ];
    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function serviceUnavailableDateAndTimeSlots()
    {
        return $this->hasMany(ServiceUnavailableDateAndTimeSlot::class);
    }
}
