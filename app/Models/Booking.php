<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    // Fillable
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'owner_id',
        'service_id',
        'time_slot_id',
        'date',
        'subtotal',
        'discount',
        'tax',
        'total',
        'advance',
        'due',
        'payment_status',
        'transaction_id',
        'payment_method',
        'status',
        'booking_type',
        'reminder_sent',
    ];
    // Relationship
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function timeSlot()
    {
        return $this->belongsTo(ServiceTimeSlot::class, 'time_slot_id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function items()
    {
        return $this->hasMany(BookingItem::class, 'booking_id');
    }
}
