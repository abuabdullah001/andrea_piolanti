<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingsSeeder extends Seeder
{
    public function run()
    {
        $bookingCount = 0;

        foreach (Service::with('time_slots')->where('owner_id', 3)->get() as $service) {
            if ($service->time_slots->isEmpty()) {
                continue; // skip if no time slots
            }

            $timeSlot = $service->time_slots->random();

            $price = (float) $service->price;
            $tax   = (float) $service->tax;
            $discount = 0;

            $subtotal = $price;
            $total    = $price + $tax - $discount;
            $advance  = 0;
            $due      = $total - $advance;

            Booking::create([
                'invoice_no'      => 'INV-' . strtoupper(Str::random(8)),
                'customer_id'     => 2,
                'owner_id'        => 3,
                'service_id'      => $service->id,
                'time_slot_id'    => $timeSlot->id,
                'date'            => Carbon::now()->addDays(rand(1, 15))->format('Y-m-d'),
                'subtotal'        => number_format($subtotal, 2),
                'discount'        => number_format($discount, 2),
                'tax'             => number_format($tax, 2),
                'total'           => number_format($total, 2),
                'advance'         => number_format($advance, 2),
                'due'             => number_format($due, 2),
                'payment_status'  => 'unpaid',
                'transaction_id'  => null,
                'payment_method'  => 'cash',
                'status'          => 'pending',
                'booking_type'    => 'standard',
                'reminder_sent'   => false,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            $bookingCount++;
        }

        echo "Seeded {$bookingCount} bookings.\n";
    }
}
