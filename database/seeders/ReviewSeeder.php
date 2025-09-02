<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        $comments = [
            'Great service!',
            'Very satisfied.',
            'Will book again.',
            'Highly recommended.',
            'Professional and on time.',
            'Service quality was top-notch.',
            'Could be better, but overall good.',
            'Loved the experience.',
            'Quick and efficient.',
            'Excellent communication.',
        ];

        $services = Service::whereBetween('id', [1, 10])->get();

        foreach ($services as $service) {
            $reviewCount = rand(1, 2);
            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'customer_id'     => 2,
                    'reviewable_id'   => $service->id,
                    'reviewable_type' => Service::class,
                    'rating'          => rand(1, 5),
                    'comment'         => $comments[array_rand($comments)],
                    'status'          => 'approved',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        echo "✅ Reviews seeded for services 1-10\n";
    }
}
