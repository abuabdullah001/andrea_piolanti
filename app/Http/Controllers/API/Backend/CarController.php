<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Str;

class CarController extends Controller
{
    use apiresponse;

    public function index()
    {
        $cars = Car::with('user')->first();
        return $this->success([
            'cars' => $cars,
            'users' => User::all(),
            'success' => true
        ]);
    }

    // Store Car
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'model' => 'required',
            'description' => 'required',
            'image' => 'required',
            'location' => 'required',
            'date'    => 'required',
            'price' => 'required',
            'user_id' => 'required',
            'favourite' => 'required|boolean',
        ]);

        $car = new Car;

        $car ->user_id    = $request->user_id;
        $car->title       = $request->title;
        $car->slug        = Str::slug($request->title);
        $car->user_id     = $request->user_id;
        $car->model       = $request->model;
        $car->brand_name  = $request->brand_name;
        $car->description = $request->description;
        $car->image       = $request->image;
        $car->location    = $request->location;
        $car->date        = date('Y-m-d', strtotime($request->date));
        $car->price       = $request->price;
        $car->favourite        = $request->favourite;

        $car->save();

        return $this->success([
            'car' => $car
        ], 'Car created successfully', 201);
    }


    
    // Update Car
    public function update(Request $request, $id)
    {
       $car = Car::find($id);

        $car ->user_id     = $request->user_id;
        $car->title       = $request->title;
        $car->slug        = Str::slug($request->title);
        $car->user_id     = $request->user_id;
        $car->model       = $request->model;
        $car->brand_name  = $request->brand_name;
        $car->description = $request->description;
        $car->image       = $request->image;
        $car->location    = $request->location;
        $car->date        = $request->date;
        $car->price       = $request->price;
        $car->favourite   = $request->favourite;
        $car->save();

        return redirect()->back()->with('success', 'Car updated successfully');
    }

    // Delete Car
    public function destroy($id)
    {
        $car = Car::findOrFail($id);
        $car->delete();
        return redirect()->back()->with('success', 'Car deleted successfully');
    }

}
