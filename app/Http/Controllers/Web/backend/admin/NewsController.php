<?php

namespace App\Http\Controllers\Web\backend\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\News;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\apiresponse;


class NewsController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $news = News::with('user')->orderBy('id', 'desc');
            return DataTables::of($news)
                ->addIndexColumn()
                ->addColumn('user_id', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('date', fn($row) => $row->date)
                ->addColumn('title', fn($row) => $row->title)
                ->addColumn('description', fn($row) => strip_tags($row->description))
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset($row->image) . '" width="80"/>';
                })


                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.news.edit', $row->id);
                    $viewUrl = route('admin.news.show', $row->id);

                    $actionBtn  = '<a href="' . $viewUrl . '" class="btn btn-info btn-sm me-1"><i class="fa fa-eye"></i></a>';
                    $actionBtn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm me-1"><i class="fa fa-edit"></i></a>';
                    $actionBtn .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete mt-1" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';

                    return $actionBtn;
                })
                ->rawColumns(['date', 'title', 'description', 'image', 'status', 'action'])
                ->make(true);
        }
        return view('backend.admin.news.index');
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

        $news = new News();

        $news->title = $request->title;
        $news->description = $request->description;
        if ($image = $request->file('image')) {
            $destinationPath = 'uploads/news/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $news->image = $destinationPath . $profileImage;
        }
        $news->user_id = auth()->id(); // ✅ FIXED
        $news->date = date('Y-m-d');
        $news->status = $request->status;

        $news->save();

        return redirect()->route('admin.news.index');
    }

    public function create()
    {
        return view('backend.admin.news.create');
    }

    public function edit($id)
    {
        $news = News::find($id);
        return view('backend.admin.news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {

        $news = News::find($id);

        $news->title = $request->title;
        $news->description = $request->description;
        if ($image = $request->file('image')) {
            $destinationPath = 'uploads/news/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $news->image = $destinationPath . $profileImage;
        }
        $news->user_id = auth()->id();
        $news->date = date('Y-m-d');
        $news->status = $request->status;

        $news->save();

        return redirect()->route('admin.news.index');
    }

    public function destroy($id)
    {
        $news = News::find($id);
        $news->delete();
        return redirect()->route('admin.news.index');
    }

    public function show($id)
    {
        $news = News::find($id);
        return view('backend.admin.news.show', compact('news'));
    }


    public function toggleStatus($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json(['success' => false]);
        }

        $news->status = $news->status === 'active' ? 'inactive' : 'active';
        $news->save();

        return response()->json([
            'success' => true,
            'status' => $news->status
        ]);
    }
}
