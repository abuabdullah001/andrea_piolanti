<?php

namespace App\Http\Controllers\Web\backend\admin;

use App\Http\Controllers\Controller;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use App\Models\NewsLetter;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class NewsLetterController extends Controller
{
    use apiresponse;

    public function index(Request $request){
        if ($request->ajax()) {
            $newsletters = NewsLetter::get();

             return DataTables::of($newsletters)
                ->addIndexColumn()
                ->addColumn('email', fn($row) => $row->email)
                ->addColumn('action', function ($newsletters) {
                    $btn = ' <a href="javascript:void(0)" class="btn btn-danger btn-sm delete" data-id="' . $newsletters->id . '"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['email', 'action'])
                ->make(true);
        }
        return view('backend.admin.newsletter.index');
    }


    public function destroy($id){
        $newsletter = NewsLetter::find($id);
        $newsletter->delete();
        return redirect()->route('admin.newsletter.index');
    }



}
