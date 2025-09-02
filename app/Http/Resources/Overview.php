<?php

namespace App\Http\Resources;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class Overview extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Query once and reuse
        $confirmedBookings = Booking::where('owner_id', Auth::id())
            ->where('status', 'confirmed')
            ->where('date', '<=', now()->toDateString())
            ->orderBy('date', 'desc')
            ->get();

        $revenuesByDay = $confirmedBookings
            ->groupBy('date')
            ->map(function ($items, $date) {
                return [
                    'day' => \Carbon\Carbon::parse($date)->format('D'),
                    'date' => \Carbon\Carbon::parse($date)->format('d-m-Y'),
                    'revenue' => $items->sum('total')
                ];
            })
            ->values();

        $currentMonthBookings = Booking::where('owner_id', Auth::id())
            ->where('status', 'confirmed')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $revenuesByMonth = [
            'year' => now()->year,
            'month' => now()->format('F'),
            'revenue' => $currentMonthBookings->sum('total')
        ];

        return [
            'new_bookings'           => Booking::where('owner_id', Auth::id())->where('status', 'pending')->count(),
            'confirmed_bookings'     => Booking::where('owner_id', Auth::id())->where('status', 'confirmed')->count(),
            'cancelled_bookings'     => Booking::where('owner_id', Auth::id())->where('status', 'cancelled')->count(),
            'rescheduled_bookings'   => Booking::where('owner_id', Auth::id())->where('status', 'rescheduled')->count(),
            'total_bookings'         => $confirmedBookings->count(),
            'Revenue'                => $confirmedBookings->sum('total'),
            'total_revenue_by_day'   => $revenuesByDay,
            'total_revenue_by_month' => $revenuesByMonth,
        ];
    }
}
