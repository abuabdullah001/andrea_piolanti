<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsLetter;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;


class NewsLetterController extends Controller
{
    use apiresponse;

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
           'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        $newsletter = new NewsLetter();
        $newsletter->email = $request->email;
        $newsletter->save();

        return $this->success([
            'email' => $newsletter->email
        ], 'Newsletter created successfully', 200);
    }
    
}
