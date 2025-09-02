<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\Feature;
use App\Models\SubscriptionPricingOption;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Starter Plan
        $starterPlan = SubscriptionPlan::create([
            'name' => 'Starter',
            'description' => 'Best for new or solo service providers.',
        ]);

        // Create Growth Plan
        $growthPlan = SubscriptionPlan::create([
            'name' => 'Growth',
            'description' => 'For growing service-based businesses.',
        ]);

        // Create Pro Plan
        $proPlan = SubscriptionPlan::create([
            'name' => 'Pro',
            'description' => 'For businesses with multiple staff or services.',
        ]);

        // Create Enterprise Plan
        $enterprisePlan = SubscriptionPlan::create([
            'name' => 'Enterprise',
            'description' => 'For large organizations with specific needs.',
        ]);

        // Create features
        $features = [
            'Unlimited Appointments',
            'Basic Theme',
            'Email Notifications',
            'Time Zone Auto-adjust',
            'Accept Payments via Stripe',
            'Invoice Generation',
            'SMS Reminders',
            'Memberships & Subscriptions',
            'Custom Booking Link',
            'Client Notes',
            'Premium Themes',
            'Multi-Time Zone Scheduling',
            'Loyalty Program',
            'Staff Management',
            'Custom Domain Support',
            'API Access',
            'Dedicated Account Manager',
            'White-labeled Branding',
            'Centralized Admin Dashboard',
            'SLA & Uptime Guarantee',
            'Advanced Analytics',
            'Additional API Endpoints',
        ];

        // Insert features into the database
        foreach ($features as $featureName) {
            Feature::create([
                'name' => $featureName,
            ]);
        }

        // Attach features to plans
        $starterFeatures = [
            'Unlimited Appointments',
            'Basic Theme',
            'Email Notifications',
            'Time Zone Auto-adjust',
            'Accept Payments via Stripe',
            'Invoice Generation',
            '3.3% + 30¢ per transaction'
        ];

        foreach ($starterFeatures as $feature) {
            $starterPlan->features()->attach(Feature::where('name', $feature)->first());
        }

        $growthFeatures = [
            'Unlimited Appointments',
            'Basic Theme',
            'Email Notifications',
            'Time Zone Auto-adjust',
            'Accept Payments via Stripe',
            'Invoice Generation',
            'SMS Reminders',
            'Memberships & Subscriptions',
            'Custom Booking Link',
            'Client Notes',
            'Premium Themes',
            '2.9% + 20¢ per transaction',
            'API Access'
        ];

        foreach ($growthFeatures as $feature) {
            $growthPlan->features()->attach(Feature::where('name', $feature)->first());
        }

        $proFeatures = [
            'Unlimited Appointments',
            'Basic Theme',
            'Email Notifications',
            'Time Zone Auto-adjust',
            'Accept Payments via Stripe',
            'Invoice Generation',
            'SMS Reminders',
            'Memberships & Subscriptions',
            'Custom Booking Link',
            'Client Notes',
            'Premium Themes',
            'Multi-Time Zone Scheduling',
            'Loyalty Program',
            'Staff Management',
            'Custom Domain Support',
            'API Access',
            '2.5% + 10¢ per transaction'
        ];

        foreach ($proFeatures as $feature) {
            $proPlan->features()->attach(Feature::where('name', $feature)->first());
        }

        $enterpriseFeatures = [
            'Unlimited Appointments',
            'Basic Theme',
            'Email Notifications',
            'Time Zone Auto-adjust',
            'Accept Payments via Stripe',
            'Invoice Generation',
            'SMS Reminders',
            'Memberships & Subscriptions',
            'Custom Booking Link',
            'Client Notes',
            'Premium Themes',
            'Multi-Time Zone Scheduling',
            'Loyalty Program',
            'Staff Management',
            'Custom Domain Support',
            'API Access',
            'Dedicated Account Manager',
            'White-labeled Branding',
            'Centralized Admin Dashboard',
            'SLA & Uptime Guarantee',
            'Advanced Analytics',
            'Additional API Endpoints',
            '1.9% + 5¢ per transaction'
        ];

        foreach ($enterpriseFeatures as $feature) {
            $enterprisePlan->features()->attach(Feature::where('name', $feature)->first());
        }

        // Create Pricing Options for each plan
        $pricingOptions = [
            ['plan' => $starterPlan, 'billing_period' => 'monthly', 'price' => 16, 'duration_days' => 30, 'stripe_price_id' => 'price_1RKdQpHImdHouCm0uoOEhzoF'],
            ['plan' => $starterPlan, 'billing_period' => 'yearly', 'price' => 160, 'duration_days' => 365, 'stripe_price_id' => 'price_1RKdRyHImdHouCm0rLOYwENO'],
            ['plan' => $growthPlan, 'billing_period' => 'monthly', 'price' => 27, 'duration_days' => 30, 'stripe_price_id' => 'price_1RKdUDHImdHouCm0KLxh4tWx'],
            ['plan' => $growthPlan, 'billing_period' => 'yearly', 'price' => 270, 'duration_days' => 365, 'stripe_price_id' => 'price_1RKdVHHImdHouCm00FAkNtac'],
            ['plan' => $proPlan, 'billing_period' => 'monthly', 'price' => 49, 'duration_days' => 30, 'stripe_price_id' => 'price_1RKdXMHImdHouCm0oADsZDNb'],
            ['plan' => $proPlan, 'billing_period' => 'yearly', 'price' => 490, 'duration_days' => 365, 'stripe_price_id' => 'price_1RKdYhHImdHouCm0fQLmoaHv'],
            ['plan' => $enterprisePlan, 'billing_period' => 'custom', 'price' => null, 'duration_days' => null, 'stripe_price_id' => null],
        ];

        foreach ($pricingOptions as $option) {
            SubscriptionPricingOption::create([
                'subscription_plan_id' => $option['plan']->id,
                'billing_period' => $option['billing_period'],
                'price' => $option['price'],
                'duration_days' => $option['duration_days'],
                'stripe_price_id' => $option['stripe_price_id']
            ]);
        }
    }
}
