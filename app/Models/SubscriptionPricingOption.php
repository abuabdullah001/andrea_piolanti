<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPricingOption extends Model
{
    // Fillable
    protected $fillable = [
        'subscription_plan_id',
        'billing_period',
        'price',
        'duration_days',
        'discount_note',
        'stripe_price_id',
    ];
    // Relationships
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'subscription_pricing_option_features');
    }
}
