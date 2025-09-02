<?php

namespace App\Http\Controllers\Web\backend\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    use apiresponse;

public function index(Request $request)
{
    if ($request->ajax()) {
        $banners = Banner::latest()->get();
        return DataTables::of($banners)
            ->addIndexColumn()
            ->addColumn('title', fn($row) => $row->title)
            ->addColumn('description', fn($row) => strip_tags($row->description))
            ->addColumn('image', fn($row) => '<img src="' . asset($row->image) . '" width="80"/>')
            ->addColumn('status', function ($row) {
                $checked = $row->status == 1 ? 'checked' : '';
                return '<div class="form-check form-switch mb-2 text-center">
                    <input class="form-check-input" type="checkbox" data-id="' . $row->id . '" ' . $checked . ' onchange="toggleStatus(this)">
                </div>';
            })
            ->addColumn('action', function ($row) {
                $edit = '<a href="' . route('admin.banner.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">
                            <i class="fa-solid fa-pen"></i>
                         </a>';
                $delete = '<button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-danger deleteBtn">
                            <i class="fa-solid fa-trash"></i>
                           </button>';
                return $edit . $delete;
            })
            ->rawColumns(['image', 'status', 'action']) // ✅ must include 'status'
            ->make(true);
    }

    return view('backend.admin.banner.index');
}


    public function create()
    {
        return view('backend.admin.banner.create');
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $banner = new Banner();

        $banner->title = $request->title;
        $banner->description = $request->description;
        if ($image = $request->file('image')) {
            $destinationPath = 'uploads/banners/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $banner->image = $destinationPath . $profileImage;
        }

        $banner->status = $request->status;

        $banner->save();

        return redirect()->route('admin.banner.index');
    }

    public function edit($id)
    {
        $banner = Banner::find($id);
        return view('backend.admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $banner = Banner::find($id);

        $banner->title = $request->title;
        $banner->description = $request->description;
        if ($image = $request->file('image')) {
            $destinationPath = 'uploads/banners/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $banner->image = $destinationPath . $profileImage;
        }

        $banner->status = $request->status;

        $banner->save();

        return redirect()->route('admin.banner.index');
    }

    public function destroy($id)
    {
        $banner = Banner::find($id);
        $banner->delete();
        return redirect()->route('admin.banner.index');
    }

public function updateStatus(Request $request, $id)
{
    $banner = Banner::find($id);
    $banner->status = $request->status;
    $banner->save();

    // return JSON response
    return response()->json([
        'success' => true,
        'status' => $banner->status
    ]);
}


}
