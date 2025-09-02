<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quick_spec;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;



class QuickSpecController extends Controller
{
    use apiresponse;

    public function index()
    {
        $quickSpecs = Quick_spec::all();
        return $this->success($quickSpecs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
             'category' => 'required',
            'transmission' => 'required',
            'miles' => 'required'  ,
            'fuelLiter' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quickSpec = new Quick_spec();

        $quickSpec->category = $request->category;
        $quickSpec->transmission = $request->transmission;
        $quickSpec->miles = $request->miles;
        $quickSpec->fuelLiter = $request->fuelLiter;

        $quickSpec->save();

        return $this->success([
            'quick_spec' => $quickSpec,
        ], 'Quick spec created successfully', 200);
    }


    public function edit($id)
    {
        $quickSpec = Quick_spec::find($id);
        return $this->success($quickSpec);
    }


    public function update(Request $request, $id)
    {
        $quickSpec = Quick_spec::find($id);

        if (!$quickSpec) {
            return $this->error([], 'Quick spec not found', 404);
        }

        $quickSpec->category = $request->category;
        $quickSpec->transmission = $request->transmission;
        $quickSpec->miles = $request->miles;
        $quickSpec->fuelLiter = $request->fuelLiter;

        $quickSpec->save();

        return $this->success([
            'quick_spec' => $quickSpec,
        ], 'Quick spec updated successfully', 200);
    }



    public function destroy($id)
    {
        $quickSpec = Quick_spec::find($id);
        $quickSpec->delete();
        return $this->success([
            'quick_spec' => $quickSpec,
        ], 'Quick spec deleted successfully', 200);
    }

}
