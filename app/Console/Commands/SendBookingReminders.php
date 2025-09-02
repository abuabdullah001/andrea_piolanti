<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;


class SendBookingReminders extends Command
{
    protected $signature = 'booking:send-reminders';
    protected $description = 'Send reminder emails and SMS before booking date'; // << Semicolon here!

    public function handle()
    {
        $reminderDaysBefore = 7; // how many days before you want reminder

        $today = now();
        $endDate = $today->copy()->addDays($reminderDaysBefore);

        $bookings = Booking::whereBetween('date', [$today->toDateString(), $endDate->toDateString()])
                           ->where('reminder_sent', false)
                           ->with('customer')
                           ->get();

        foreach ($bookings as $booking) {
            if ($booking->customer) {
                Mail::to($booking->customer->email)
                    ->send(new BookingReminderMail($booking));

                SmsService::send($booking->customer->phone, "Reminder: Your booking is on " . $booking->date);

                $booking->update(['reminder_sent' => true]);
            }
        }
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->daily();
    }
}
