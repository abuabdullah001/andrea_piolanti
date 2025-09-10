<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Char_external;

class CharExternalController extends Controller
{
    use apiresponse;

    public function index(){
        $data = Char_external::all();
        return $this->success([
            'char_external' => $data
        ], ' Char external get successfully', 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }

        $char_external = new Char_external;
        $char_external->name = $request->name;
        $char_external->car_id = $request->car_id;
        $char_external->save();

        return $this->success([
            'char_external' => $char_external
        ], ' Char external created successfully', 200);
    }

    public function edit($id){
        $char_external = Char_external::find($id);
        return $this->success([
            'char_external' => $char_external
        ]);
    }

    public function update(Request $request, $id){
        $char_external = Char_external::find($id);
        if (!$char_external) {
            return $this->error([], 'Char external not found', 404);
        }
        $char_external->name = $request->name;
        $char_external->car_id = $request->car_id;
        $char_external->save();
        return $this->success([
            'char_external' => $char_external,
        ], ' Char external updated successfully', 200);
    }


    public function destroy($id){
        $char_external = Char_external::find($id);
        $char_external->delete();
        return $this->success([
            'char_external' => $char_external,
        ], ' Char external deleted successfully', 200);

    }

}
