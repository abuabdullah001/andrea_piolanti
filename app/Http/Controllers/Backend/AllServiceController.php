<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AllServiceController extends Controller
{
    public function get($id)
    {
        $service = Service::with('owner', 'time_slots')->find($id);
        if ($service) {
            return response()->json($service);
        } else {
            return response()->json(['error' => 'Service not found'], 404);
        }
    }
    // Index
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    return '<img src="' . asset($data->image) . '" width="24" alt="">';
                })
                ->addColumn('service_by', function ($data) {
                    return $data->owner->name ?? 'N/A';
                })
                ->addColumn('totalBookings', function ($data) {
                    return Booking::where('service_id', $data->id)
                    ->where('status', 'confirmed')
                    ->count();
                })
                ->addColumn('action', function ($data) {
                    return '<a href="javascript:void(0);" class="btn btn-sm btn-primary" onclick="viewService(' . $data->id . ')"><i class="fa fa-eye"></i> View</a>';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('backend.layout.service.index');
    }
}
