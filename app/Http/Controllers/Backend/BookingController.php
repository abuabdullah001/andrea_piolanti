<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BookingController extends Controller
{
    // Index
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Booking::with(['customer', 'service', 'owner', 'items'])->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('booking_type', function($row) {
                    return $row->booking_type === 'custom'
                        ? '<span class="badge badge-info">Custom</span>'
                        : '<span class="badge badge-secondary">Standard</span>';
                })
                ->addColumn('date', function($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->addColumn('service_info', function($row) {
                    return '<img src="'.asset($row->service->image).'" width="40" class="rounded mr-2">
                            <strong>'.$row->service->title.'</strong>';
                })
                ->addColumn('service_price', function($row) {
                    return '$' . $row->service->price;
                })
                ->addColumn('owner_info', function($row) {
                    $image = asset('user.png');
                    return '<img src="'.$image.'" width="30" class="rounded-circle mr-1">
                            <span>'.$row->owner->name.'</span>';
                })
                ->addColumn('items', function($row) {
                    if ($row->items->isEmpty()) return 'N/A';
                    $html = '<table class="table table-sm mb-0">';
                    foreach ($row->items as $item) {
                        $html .= '<tr><td>' . $item->description . '</td><td>$' . $item->price . '</td></tr>';
                    }
                    $html .= '</table>';
                    return $html;
                })
                ->addColumn('subtotal', function($row) {
                    return '$' . $row->subtotal;
                })
                ->addColumn('tax', function($row) {
                    return '$' . 0;
                })
                ->addColumn('total', function($row) {
                    return '$' . $row->total;
                })
                ->addColumn('status', function($row) {
                    $statusClass = match ($row->status) {
                        'pending' => 'badge-warning',
                        'confirmed' => 'badge-success',
                        default => 'badge-secondary'
                    };
                    return '<span class="text-uppercase badge '.$statusClass.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row) {
                    return '<a href="'.route('booking.view', $row->id).'" class="btn btn-sm btn-primary text-uppercase">
                                <i class="fa fa-eye"></i> View
                            </a>';
                })
                ->rawColumns(['booking_type', 'service_info', 'owner_info', 'items', 'status', 'action'])
                ->make(true);
        }

        return view('backend.booking.index');
    }

    public function view($id)
    {
        $booking = Booking::with(['customer', 'service', 'owner', 'items'])->findOrFail($id);
        return view('backend.booking.view', compact('booking'));
    }
}
