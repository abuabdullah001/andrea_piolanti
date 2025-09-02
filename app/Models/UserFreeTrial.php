<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFreeTrial extends Model
{
    // Fillable
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'is_active',
    ];
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
