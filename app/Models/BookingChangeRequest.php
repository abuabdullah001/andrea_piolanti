<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingChangeRequest extends Model
{
    // Fillable
    protected $fillable = [
        'booking_id',
        'customer_id',
        'type',
        'requested_date',
        'requested_time_slot',
        'reason',
        'status',
        'responded_by',
        'response_note'
    ];
    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
    public function timeSlot()
    {
        return $this->belongsTo(ServiceTimeSlot::class, 'requested_time_slot');
    }
}
