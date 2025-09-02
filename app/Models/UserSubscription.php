<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    // Fillable
    protected $fillable = [
        'user_id',
        'subscription_pricing_option_id',
        'start_date',
        'end_date',
        'is_active',
    ];
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subscriptionPricingOption()
    {
        return $this->belongsTo(SubscriptionPricingOption::class);
    }
}
