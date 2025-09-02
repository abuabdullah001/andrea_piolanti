<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    // Fillable
    protected $fillable = [
        'name',
        'description',
        'status',
    ];
    // Relationships
    public function pricingOptions()
    {
        return $this->hasMany(SubscriptionPricingOption::class);
    }
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'subscription_plan_features');
    }
}
