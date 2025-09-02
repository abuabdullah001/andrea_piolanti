<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanFeatur extends Model
{
    // Fillable
    protected $fillable = [
        'subscription_plan_id',
        'feature_id',
    ];
    // Relationships
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
