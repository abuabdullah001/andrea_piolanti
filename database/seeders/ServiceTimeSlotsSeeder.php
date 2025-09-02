<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Service;
use App\Models\ServiceTimeSlot;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ServiceTimeSlotsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $serviceId) {
            $slotsCount = rand(1, 3); // 1 to 3 slots per service

            for ($i = 0; $i < $slotsCount; $i++) {
                ServiceTimeSlot::create([
                    'service_id' => $serviceId,
                    'time'       => $faker->time('H:i'),
                    'status'     => 'active',
                ]);
            }
        }
    }
}
