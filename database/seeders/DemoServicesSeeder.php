<?php

namespace Database\Seeders;

use App\Models\Favourite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DemoServicesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $title = $faker->sentence(3);

            Service::create([
                'owner_id'          => 3,
                'title'             => $title,
                'slug'              => Str::slug($title) . '-' . Str::random(5),
                'description'       => $faker->paragraph,
                'duration'          => $faker->numberBetween(30, 120) . ' minutes',
                'price'             => $faker->randomFloat(2, 20, 200),
                'is_deposite'       => $faker->randomElement(['yes', 'no']),
                'minimum_deposite'  => $faker->randomFloat(2, 5, 50),
                'tax'               => $faker->randomFloat(2, 0, 20),
                'service_at'        => $faker->randomElement(['person', 'virtual']),
                'location'          => $faker->address,
                'image'             => 'default.jpg',
                'status'            => 'active',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }


        $userId = 2;

        foreach ([1, 3, 5, 7, 9] as $serviceId) {
            Favourite::create([
                'user_id'    => $userId,
                'service_id' => $serviceId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "Seeded favourites for user_id 2 (services: 1, 3, 5, 7, 9).\n";
    }
}
