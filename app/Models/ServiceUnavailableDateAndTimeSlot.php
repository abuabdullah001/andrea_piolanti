<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceUnavailableDateAndTimeSlot extends Model
{
    // Fillable
    protected $fillable = [
        'service_id',
        'date',
        'time_slot_id',
        'reason',
    ];
    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function timeSlot()
    {
        return $this->belongsTo(ServiceTimeSlot::class);
    }
}
