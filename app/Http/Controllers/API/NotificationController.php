<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use apiresponse;
    public function notification(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        // Get the notifications
        $notifications = $user->notifications;

        // Check if a date is provided in the request
        if ($request->filled('date')) {
            $filterDate = Carbon::parse($request->date)->toDateString();

            // Filter by created_at date
            $notifications = $notifications->filter(function ($notification) use ($filterDate) {
                return Carbon::parse($notification->created_at)->toDateString() === $filterDate;
            });
        }

        // Only return the 'data' field
        $onlyData = $notifications->map(function ($notification) {
            return $notification->data;
        })->values();

        return $this->success($onlyData, 'Notification data fetched successfully', 200);
    }

}
