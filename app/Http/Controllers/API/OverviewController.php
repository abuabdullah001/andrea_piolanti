<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Overview;
use App\Models\Booking;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OverviewController extends Controller
{
    use apiresponse;

    //=======================================
    // Business Owner Methods
    //=======================================

    // Overview
    function overviews()
    {
        $ownerId = Auth::id();

        // 1. Confirmed bookings for the whole year (for monthly revenue calculation)
        $confirmedBookings = Booking::where('owner_id', $ownerId)
            ->where('status', 'confirmed')
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get();

        // 2. Revenue by Day (only current month)
        $revenuesByDay = Booking::where('owner_id', $ownerId)
            ->where('status', 'confirmed')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date')
            ->map(function ($items, $date) {
                return [
                    'day' => \Carbon\Carbon::parse($date)->format('D'),
                    'date' => \Carbon\Carbon::parse($date)->format('d-m-Y'),
                    'revenue' => $items->sum('total')
                ];
            })
            ->values();

        // 3. Revenue by Month (all year)
        $revenuesByMonth = $confirmedBookings
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->date)->format('F');
            })
            ->map(function ($items, $month) {
                return [
                    'month'   => $month,
                    'year'    => now()->year,
                    'revenue' => $items->sum('total')
                ];
            })
            ->values();

        // 4. Filters - TODAY, THIS WEEK, THIS MONTH

        // today
        $today = now()->toDateString();

        // week (monday to sunday)
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();

        // month
        $month = now()->month;
        $year = now()->year;

        // new bookings
        $newBookingsToday = Booking::where('owner_id', $ownerId)->where('status', 'pending')->whereDate('date', $today)->count();
        $newBookingsWeek = Booking::where('owner_id', $ownerId)->where('status', 'pending')->whereBetween('date', [$weekStart, $weekEnd])->count();
        $newBookingsMonth = Booking::where('owner_id', $ownerId)->where('status', 'pending')->whereMonth('date', $month)->whereYear('date', $year)->count();

        // confirmed bookings
        $confirmedToday = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereDate('date', $today)->count();
        $confirmedWeek = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereBetween('date', [$weekStart, $weekEnd])->count();
        $confirmedMonth = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereMonth('date', $month)->whereYear('date', $year)->count();

        // cancelled bookings
        $cancelledToday = Booking::where('owner_id', $ownerId)->where('status', 'cancelled')->whereDate('date', $today)->count();
        $cancelledWeek = Booking::where('owner_id', $ownerId)->where('status', 'cancelled')->whereBetween('date', [$weekStart, $weekEnd])->count();
        $cancelledMonth = Booking::where('owner_id', $ownerId)->where('status', 'cancelled')->whereMonth('date', $month)->whereYear('date', $year)->count();

        // rescheduled bookings
        $rescheduledToday = Booking::where('owner_id', $ownerId)->where('status', 'rescheduled')->whereDate('date', $today)->count();
        $rescheduledWeek = Booking::where('owner_id', $ownerId)->where('status', 'rescheduled')->whereBetween('date', [$weekStart, $weekEnd])->count();
        $rescheduledMonth = Booking::where('owner_id', $ownerId)->where('status', 'rescheduled')->whereMonth('date', $month)->whereYear('date', $year)->count();

        // total bookings
        $totalBookingsToday = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereDate('date', $today)->count();
        $totalBookingsWeek = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereBetween('date', [$weekStart, $weekEnd])->count();
        $totalBookingsMonth = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereMonth('date', $month)->whereYear('date', $year)->count();

        // Revenues
        $totalRevenueToday = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereDate('date', $today)->sum('total');
        $totalRevenueWeek = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereBetween('date', [$weekStart, $weekEnd])->sum('total');
        $totalRevenueMonth = Booking::where('owner_id', $ownerId)->where('status', 'confirmed')->whereMonth('date', $month)->whereYear('date', $year)->sum('total');

        // Clients Count

        // Clients Count

        $clientsLastWeek = Booking::where('owner_id', $ownerId)
            ->where('status', 'confirmed')
            ->whereBetween('date', [now()->subDays(6)->toDateString(), now()->toDateString()])
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('date')
            ->map(function ($items, $date) {
                return [
                    'day'     => \Carbon\Carbon::parse($date)->format('D'),
                    'date'    => \Carbon\Carbon::parse($date)->format('d-m-Y'),
                    'clients' => $items->unique('customer_id')->count(), // fixed here
                ];
            })
            ->values();


        $clientsByMonth = Booking::where('owner_id', $ownerId)
            ->where('status', 'confirmed')
            ->whereYear('date', now()->year)
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->date)->format('F');
            })
            ->map(function ($items, $month) {
                return [
                    'month'   => $month,
                    'year'    => now()->year,
                    'clients' => $items->unique('customer_id')->count(), // fixed here
                ];
            })
            ->values();


        $clientsByYear = Booking::where('owner_id', $ownerId)
            ->where('status', 'confirmed')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y');
            })
            ->map(function ($items, $year) {
                return [
                    'year'    => $year,
                    'clients' => $items->unique('customer_id')->count(), // fixed here
                ];
            })
            ->values();


        // =====================================================
        // Transactions Conditions
        // =====================================================

        $futureConfirmedBookings = Booking::where('owner_id', $ownerId)
            ->where('status', 'confirmed')
            ->whereDate('date', '>=', $today)
            ->with(['customer', 'service'])
            ->get();

        $totalAmount = $futureConfirmedBookings->sum('total');
        $totalAdvance = $futureConfirmedBookings->sum('advance');
        $totalDue = $futureConfirmedBookings->sum('due');

        // Completed Transactions (Advance Paid)
        $completedTransactions = $futureConfirmedBookings->filter(function ($booking) {
            return $booking->advance > 0;
        })->map(function ($booking) {
            return [
                'customer_name' => $booking->customer->name ?? 'N/A',
                'service_name' => $booking->service->title ?? 'N/A',
                'advance_amount' => $booking->advance,
                'created_at' => $booking->created_at->format('d-m-Y H:i:s'),
            ];
        })->values();

        // Pending Transactions (Due Left)
        $pendingTransactions = $futureConfirmedBookings->filter(function ($booking) {
            return $booking->due > 0;
        })->map(function ($booking) {
            return [
                'customer_name' => $booking->customer->name ?? 'N/A',
                'service_name' => $booking->service->title ?? 'N/A',
                'due_amount' => $booking->due,
                'created_at' => $booking->created_at->format('d-m-Y H:i:s'),
            ];
        })->values();

        // Calculate Percentages
        $completedPercentage = $totalAmount > 0 ? round(($totalAdvance / $totalAmount) * 100, 2) : 0;
        $pendingPercentage = $totalAmount > 0 ? round(($totalDue / $totalAmount) * 100, 2) : 0;

        // =====================================================
        // Transactions Conditions End
        // =====================================================




        $data = [
            'filters' => [
                'today' => [
                    'new_bookings'         => $newBookingsToday,
                    'confirmed_bookings'   => $confirmedToday,
                    'cancelled_bookings'   => $cancelledToday,
                    'rescheduled_bookings' => $rescheduledToday,
                    'totalBookingsToday'   => $totalBookingsToday,
                    'totalRevenueToday'    => $totalRevenueToday,
                ],
                'this_week' => [
                    'new_bookings'         => $newBookingsWeek,
                    'confirmed_bookings'   => $confirmedWeek,
                    'cancelled_bookings'   => $cancelledWeek,
                    'rescheduled_bookings' => $rescheduledWeek,
                    'totalBookingsWeek'    => $totalBookingsWeek,
                    'totalRevenueWeek'     => $totalRevenueWeek,
                ],
                'this_month' => [
                    'new_bookings'         => $newBookingsMonth,
                    'confirmed_bookings'   => $confirmedMonth,
                    'cancelled_bookings'   => $cancelledMonth,
                    'rescheduled_bookings' => $rescheduledMonth,
                    'totalBookingsMonth'   => $totalBookingsMonth,
                    'totalRevenueMonth'    => $totalRevenueMonth,
                ],
            ],
            'clients' => [
                'last_week' => $clientsLastWeek,
                'by_month'  => $clientsByMonth,
                'by_year'   => $clientsByYear,
            ],
            'transactions' => [
                'completed'            => $completedTransactions,
                'completed_percentage' => $completedPercentage,
                'pending'              => $pendingTransactions,
                'pending_percentage'   => $pendingPercentage,
                'total'                => $totalAmount,
            ],
            'total_revenue_by_day'   => $revenuesByDay,
            'total_revenue_by_month' => $revenuesByMonth,
        ];

        return $this->success($data, 'Overview Data', 200);
    }

    //=======================================
    // Business Owner Methods
    //=======================================
}
