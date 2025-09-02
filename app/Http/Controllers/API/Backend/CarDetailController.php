<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car_detail;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;

class CarDetailController extends Controller
{
    use apiresponse;
    public function index()
    {
        $carDetails = Car_detail::all();
        return $this->success($carDetails);
    }



    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'body_type' => 'required',
            'condition' => 'required',
            'year' => 'required',
            'cylinders' => 'required',
            'mileage' => 'required',
            'transmission' => 'required',
            'displacement' => 'required',
            'color' => 'required',
            'fuel_type' => 'required',
            'drive_type' => 'required',
            'doors' => 'required',
            'vin' => 'required',

            // body_type	condition	year	cylinders	mileage 	transmission	displacement	color	fuel_type	drive_type	doors	vin

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $carDetail = new Car_detail();

        $carDetail->body_type = $request->body_type;
        $carDetail->condition = $request->condition;
        $carDetail->year = $request->year;
        $carDetail->cylinders = $request->cylinders;
        $carDetail->mileage = $request->mileage;
        $carDetail->transmission = $request->transmission;
        $carDetail->displacement = $request->displacement;
        $carDetail->color = $request->color;
        $carDetail->fuel_type = $request->fuel_type;
        $carDetail->drive_type = $request->drive_type;
        $carDetail->doors = $request->doors;
        $carDetail->vin = $request->vin;

        $carDetail->save();


        return $this->success([
            'car_detail' => $carDetail,
        ], 'Car detail created successfully', 200);
    }

    public function edit($id)
    {
        $carDetail = Car_detail::find($id);
        return $this->success($carDetail);
    }




public function update(Request $request, $id)
{

    // dd($request->all());
    $carDetail = Car_detail::find($id);

    // dd($carDetail->body_type);
    if (!$carDetail) {
        return $this->error([], 'Car detail not found', 404);
    }

    $carDetail->body_type = $request->body_type;
    $carDetail->condition = $request->condition;
    $carDetail->year = $request->year;
    $carDetail->cylinders = $request->cylinders;
    $carDetail->mileage = $request->mileage;
    $carDetail->transmission = $request->transmission;
    $carDetail->displacement = $request->displacement;
    $carDetail->color = $request->color;
    $carDetail->fuel_type = $request->fuel_type;
    $carDetail->drive_type = $request->drive_type;
    $carDetail->doors = $request->doors;
    $carDetail->vin = $request->vin;

    // ✅ Save changes
    $carDetail->save();

    return $this->success([
        'car_detail' => $carDetail,
    ], 'Car detail updated successfully', 200);
}



    public function destroy($id)
    {
        $carDetail = Car_detail::find($id);
        $carDetail->delete();
        return $this->success([
            'car_detail' => $carDetail,
        ], 'Car detail deleted successfully', 200);
    }




}
