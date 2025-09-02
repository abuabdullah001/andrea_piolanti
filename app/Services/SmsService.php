<?php

namespace App\Services;

class SmsService
{
    public static function send($phone, $message)
    {
        // Here you would integrate with real SMS provider
        logger("Sending SMS to {$phone}: {$message}");

        // Example: Twilio, Nexmo, etc.
    }
}
