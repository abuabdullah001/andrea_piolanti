<?php

namespace App\Http\Controllers\Web\backend\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mission;
use App\Traits\apiresponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class MissionController extends Controller
{
    use apiresponse;

    public function index()
    {
        if (request()->ajax()) {
            $missions = Mission::all();
            return DataTables::of($missions)
                ->addIndexColumn()
                ->addColumn('title', fn($row) => $row->title)
                ->addColumn('description', fn($row) => strip_tags($row->description))
                ->addColumn('status', function ($row) {
                    $color = $row->status === 'active' ? 'success' : 'secondary';
                    return '<span class="badge bg-' . $color . ' status-toggle" data-id="' . $row->id . '" style="cursor:pointer;">'
                        . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin.mission.edit', $row->id) . '" class="edit btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                    // Get the first mission ID dynamically
                    $firstMissionId = Mission::orderBy('id')->first()->id ?? null;

                    if ($row->id != $firstMissionId) {
                        $btn .= ' <a href="javascript:void(0)" class="btn btn-danger btn-sm delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['title', 'description', 'status', 'action'])
                ->make(true);
        }
        return view('backend.admin.mission.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        $mission = new Mission();
        $mission->title = $request->title;
        $mission->description = $request->description;
        $mission->status = $request->status ?? 'active';
        $mission->save();

        return redirect()->route('admin.mission.index')->with('success', 'Mission created successfully');
    }

    public function create()
    {
        return view('backend.admin.mission.create');
    }

    public function edit($id)
    {
        $mission = Mission::find($id);
        return view('backend.admin.mission.edit', compact('mission'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        $mission = Mission::find($id);
        $mission->title = $request->title;
        $mission->description = $request->description;
        $mission->status = $request->status ?? 'active';
        $mission->save();

        return redirect()->route('admin.mission.index')->with('success', 'Mission updated successfully');
    }

    public function destroy($id)
    {
        $mission = Mission::find($id);

        if (!$mission) {
            return response()->json([
                'success' => false,
                'message' => 'Mission not found'
            ], 404);
        }

        $mission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mission deleted successfully'
        ]);
    }


    public function toggleStatus($id)
    {
        $mission = Mission::find($id);

        if (!$mission) {
            return response()->json([
                'success' => false,
                'message' => 'Mission not found'
            ], 404);
        }

        $mission->status = $mission->status === 'active' ? 'inactive' : 'active';
        $mission->save();

        return response()->json([
            'success' => true,
            'status' => $mission->status,
            'message' => 'Mission status updated successfully!'
        ]);
    }
}
