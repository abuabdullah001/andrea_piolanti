<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Char_comfort;

class CharComfortController extends Controller
{
    use apiresponse;
    public function index(){
        $char_comfort = Char_comfort::all();
        return $this->success([
            'char_comfort' => $char_comfort
        ], ' Char comfort get successfully', 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors());
        }
     
        $char_comfort= new Char_comfort;

        $char_comfort->name = $request->name;
        $char_comfort->car_id = $request->car_id;
        $char_comfort->save();
        
        

        return $this->success([
            'char_comfort' => $char_comfort
        ], ' Char comfort created successfully', 200);

    }

    public function edit($id){
        $char_comfort = Char_comfort::find($id);
        return $this->success($char_comfort);
    }

    public function update(Request $request, $id){


        $char_comfort = Char_comfort::find($id);

        if (!$char_comfort) {
            return $this->error([], 'Char comfort not found', 404);
        }

        $char_comfort->name = $request->name;
        $char_comfort->car_id = $request->car_id;
        $char_comfort->save();
        
        return $this->success([
            'char_comfort' => $char_comfort,
        ], ' Char comfort updated successfully', 200);

    }

    public function destroy($id){
        $char_comfort = Char_comfort::find($id);
        $char_comfort->delete();
        return $this->success([
            'char_comfort' => $char_comfort,
        ], ' Char comfort deleted successfully', 200);
    }



}
