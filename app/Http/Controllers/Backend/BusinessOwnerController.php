<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BusinessOwnerController extends Controller
{
    // Index
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::role('owner')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('avatar', function ($data) {
                    return '<img src="' . asset($data->avatar) . '" width="24" alt="">';
                })
                ->addColumn('total_services', function ($data) {
                    return $data->services->count();
                })
                ->addColumn('total_bookings', function ($data) {
                    return $data->bookings
                        ->where('status', 'confirmed')
                        ->where('date', '<', now()->toDateString())
                        ->count();
                })
                ->addColumn('total_earning', function ($data) {
                    $totalearning = $data->bookings
                        ->where('status', 'completed')
                        ->where('date', '<', now()->toDateString())
                        ->sum('total');
                    return '<span class="text-success">' . $totalearning . '</span>';
                })
                ->addColumn('rating', function ($data) {
                    return '<span class="text-success"><i class="fa fa-star"></i> 4.9 (12 Reviews)</i></span>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="statusCategory(' . $data->id . ')" type="checkbox" ' . ($data->status == 'active' ? 'checked' : '') . '>
                            </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<a href="' . route('businessowners.profile', $data->username) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View Profile</a>';
                })
                ->rawColumns(['avatar', 'total_services', 'total_bookings', 'total_earning', 'rating', 'status', 'action'])
                ->make(true);
        }

        return view('backend.business_owner.index');
    }
    public function profile($username){
        $user = User::where('username', $username)->first();
        return view('backend.business_owner.profile', compact('user'));
    }

    public function status(Request $request)
    {
        $cate = Service::find($request->id);


        if ($cate->status == 'active') {
            $cate->update([
                'status' => 'inactive',
            ]);
        } else {
            $cate->update([
                'status' => 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status Updated'
        ]);
    }
}
