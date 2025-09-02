<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    // Fillable
    protected $fillable = [
        'name',
        'description',
    ];
    // Relationships
    public function subscriptionPlans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'subscription_plan_features');
    }
    public function subscriptionPricingOptions()
    {
        return $this->belongsToMany(SubscriptionPricingOption::class, 'subscription_pricing_option_features');
    }
}
