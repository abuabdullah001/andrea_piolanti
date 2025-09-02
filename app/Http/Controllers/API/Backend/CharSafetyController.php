<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Char_sefety;

class CharSafetyController extends Controller
{
    use apiresponse;

    public function index(){
        $char_safety = Char_sefety::all();
        return $this->success([
            'char_safety' => $char_safety
        ], ' Char safety fetched successfully', 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
        $char_safety = new Char_sefety;
        $char_safety->name = $request->name;
        $char_safety->save();

        return $this->success([
            'char_safety' => $char_safety
        ], ' Char safety created successfully', 200);
    }

    public function update(Request $request, $id){
        $char_safety = Char_sefety::find($id);
        if (!$char_safety) {
            return $this->error([], 'Char safety not found', 404);
        }
        $char_safety->name = $request->name;
        $char_safety->save();
        return $this->success([
            'char_safety' => $char_safety,
        ], ' Char safety updated successfully', 200);
    }

    public function destroy($id){
        $char_safety = Char_sefety::find($id);
        $char_safety->delete();
        return $this->success([
            'char_safety' => $char_safety,
        ], ' Char safety deleted successfully', 200);
    }


}
