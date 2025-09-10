<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Traits\apiresponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class ContactController extends Controller
{
      use apiresponse;
      
   public function index(Request $request)
    {
        if ($request->ajax()) {
            $contacts = Contact::latest()->get();

            return DataTables::of($contacts)
                ->addIndexColumn()
                ->addColumn('announce', fn($row) => $row->announce ? 'Yes' : 'No')
                ->addColumn('message', fn($row) => $row->message ? 'Yes' : 'No')
                ->addColumn('action', function ($row) {
                    // $showUrl = route('admin.contact.show', $row->id);
                    // $actionBtn = '<a href="' . $showUrl . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>';
                    $actionBtn= '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                    return $actionBtn;
                })
                ->make(true);
        }
        return view('backend.admin.contact.index');
    }

    public function destroy($id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        return response()->json(['success' => 'Contact deleted successfully']);
    }


    
}






    

    

