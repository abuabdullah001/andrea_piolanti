<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDueRequest extends Model
{
    // Fillable
    protected $fillable = [
        'booking_id', 'owner_id' ,'customer_id', 'service_id', 'requested_payable_amount', 'full_payable_amount', 'note', 'requested_at', 'status', 'paid_at',
    ];

    // Relations

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
