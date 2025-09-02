<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPaymentMethodInfo extends Model
{
    // Fillable
    protected $fillable = ['user_id', 'type', 'card_holder_name', 'card_number', 'expiry_date', 'cvv'];
}
