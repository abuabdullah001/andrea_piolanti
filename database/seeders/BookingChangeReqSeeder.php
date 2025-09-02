<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingChangeRequest;
use App\Models\User;
use App\Notifications\BookingNotifications;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;

class BookingChangeReqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Booking::with('service.time_slots')->where('customer_id', 2)->get() as $booking) {
            $type = rand(0, 1) ? 'cancel' : 'reschedule';

            $changeRequest = new BookingChangeRequest();
            $changeRequest->booking_id = $booking->id;
            $changeRequest->customer_id = 2;
            $changeRequest->type = $type;
            $changeRequest->reason = 'Customer needs to ' . $type . ' the appointment';
            $changeRequest->status = 'pending';

            $customer = User::find(2);
            $title = $booking->service->title ?? 'Service';

            if ($type === 'reschedule') {
                $timeSlot = $booking->service->time_slots->random();
                $rescheduleDate = Carbon::now()->addDays(rand(2, 15)); // generate once
                $changeRequest->requested_date = $rescheduleDate->format('Y-m-d');
                $changeRequest->requested_time_slot = $timeSlot->id;

                Notification::send($customer, new BookingNotifications(
                    'Booking Change Request',
                    'You have rescheduled your ' . $title . ' on ' . $rescheduleDate->format('Y-m-d') . ' at ' . $timeSlot->time,
                    $title,
                    now()
                ));
            } else {
                Notification::send($customer, new BookingNotifications(
                    'Booking Change Request',
                    'You have cancelled request for your ' . $title . ' on ' . now()->format('Y-m-d'),
                    $title,
                    now()
                ));
            }

            $changeRequest->save();
        }

        echo "Booking change requests seeded successfully.\n";
    }
}
