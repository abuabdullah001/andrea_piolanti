<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Models\UserFreeTrial;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display Admin Panel
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data['totalCustomers'] = User::role('user')->count();
        $data['customers'] = User::role('user')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->withCount(['bookings as total_bookings' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->orderByDesc('total_bookings')
            ->take(5)
            ->get();
        $data['topCustomers'] = User::role('user')
            ->withCount(['bookings as confirmed_count' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->withSum(['bookings as confirmed_total' => function ($q) {
                $q->where('status', 'confirmed');
            }], 'total')
            ->orderByDesc('confirmed_total')
            ->take(5)
            ->get();

        $data['totalOwners'] = User::role('owner')->count();
        $data['owners'] = User::role('owner')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->withCount(['bookings as total_bookings' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->orderByDesc('total_bookings')
            ->take(5)
            ->get();


        $data['totalServices'] = Service::count();
        $data['topServices'] = Service::with('owner')
            ->withCount(['bookings as total_bookings' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->orderByDesc('total_bookings')
            ->take(5)
            ->get();


        $data['subscriptions_users'] = UserSubscription::with([
            'user',
            'subscriptionPricingOption.subscriptionPlan'
        ])->get();


        return view('backend.dashboard', $data);
    }
}
