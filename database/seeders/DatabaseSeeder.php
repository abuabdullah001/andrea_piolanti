<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SystemSetting;
use App\Models\Topic;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(SubscriptionPlansSeeder::class);

        // Demo Data
        $this->call(DemoServicesSeeder::class);
        $this->call(ServiceTimeSlotsSeeder::class);
        $this->call(BookingsSeeder::class);
        $this->call(ReviewSeeder::class);
        $this->call(BookingChangeReqSeeder::class);





        Category::insert([
            [
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'priority' => 0,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Beauty & Wellness',
                'slug' => 'beauty-wellness',
                'priority' => 1,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Fitness & Health',
                'slug' => 'fitness-health',
                'priority' => 2,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Home Services',
                'slug' => 'home-services',
                'priority' => 3,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Events & Entertainment',
                'slug' => 'events-entertainment',
                'priority' => 4,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Education & Coaching',
                'slug' => 'education-coaching',
                'priority' => 5,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Food & Beverage',
                'slug' => 'food-beverage',
                'priority' => 6,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Pet Services',
                'slug' => 'pet-services',
                'priority' => 7,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Auto & Mobile',
                'slug' => 'auto-mobile',
                'priority' => 8,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Tech & Digital Services ',
                'slug' => 'tech-digital-services',
                'priority' => 9,
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Home Rental & Hosting',
                'slug' => 'home-rental-hosting',
                'priority' => 10,
                'image' => 'default.jpg',
            ]
        ]);

        SystemSetting::create([
            'system_title'         => 'My System',
            'system_short_title'   => 'MS',
            'company_name'         => 'My Company',
            'tag_line'             => 'Your tagline here',
            'phone_code'           => '+1',
            'phone_number'         => '1234567890',
            'whatsapp'             => '1234567890',
            'email'                => 'info@example.com',
            'time_zone'            => 'UTC',
            'language'             => 'en',
            'admin_title'          => 'Admin Panel',
            'admin_short_title'    => 'AP',
            'copyright_text'       => '© 2025 My Company. All rights reserved.',
            'admin_copyright_text' => '© 2025 My Company. All rights reserved.',
        ]);
    }
}
