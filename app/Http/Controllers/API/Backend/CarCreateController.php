<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CarCreateController extends Controller
{
    use apiresponse;

    public function store(Request $request)
    {




        $car = new Car;

        $car->user_id     = $request->user_id;
        $car->title       = $request->title;
        $car->slug        = Str::slug($request->title);
        $car->user_id     = $request->user_id;
        $car->model       = $request->model;
        $car->brand_name  = $request->brand_name;
        $car->description = $request->description;
        if ($image = $request->file('image')) {
            $destinationPath = 'uploads/cars/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $car->image = $destinationPath . $profileImage;
        }
        $car->location    = $request->location;
        $car->date        = $request->date;
        $car->price       = $request->price;

        // ✅ Auto-generate slug
        $car->slug = Str::slug($request->title ?? $request->name);

        // ✅ Convert date to MySQL format
        if ($request->filled('date')) {
            try {
                $car->date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
            } catch (\Exception $e) {
                return $this->error(['date' => 'Invalid date format. Use d/m/Y'], 'Validation Error', 422);
            }
        }

        $car->save();

        // ✅ Save car_detail if provided
        $car->car_detail()->save(new \App\Models\Car_detail([
            'car_id' => $car->id,
            'body_type' => $request->body_type,
            'condition' => $request->condition,
            'year' => $request->year,
            'cylinders' => $request->cylinders,
            'mileage' => $request->mileage,
            'transmission' => $request->transmission,
            'displacement' => $request->displacement,
            'color' => $request->color,
            'fuel_type' => $request->fuel_type,
            'drive_type' => $request->drive_type,
            'doors' => $request->doors,
            'vin' => $request->vin
        ]));



        // ✅ Save multiple images
        if ($request->hasFile('multi_images')) {
            $paths = [];

            foreach ($request->file('multi_images') as $img) {
                $imageName = time() . '_' . uniqid() . '.' . $img->extension();
                $img->move(public_path('uploads/multi_images'), $imageName);

                $paths[] = 'uploads/multi_images/' . $imageName;
            }

            // Save all images in one row as JSON
            $car->multi_images()->create([
                'images' => json_encode($paths)
            ]);
        }




        // ✅ Save related characteristics if provided
        $car->char_internal()->save(new \App\Models\Char_internal([
            'name' => $request->char_internal_name
        ]));

        $car->char_external()->save(new \App\Models\Char_external([
            'name' => $request->char_external_name
        ]));

        $car->char_comfort()->save(new \App\Models\Char_comfort([
            'name' => $request->char_comfort_name
        ]));

        $car->char_sefety()->save(new \App\Models\Char_sefety([
            'name' => $request->char_sefety_name
        ]));

        return $this->success(
            ['car' => $car->load(['car_detail', 'multi_images', 'char_internal', 'char_external', 'char_comfort', 'char_sefety'])],
            'Car saved successfully'
        );
    }

    public function index()
    {
        $cars = Car::with(['car_detail', 'multi_images', 'char_internal', 'char_external', 'char_comfort', 'char_sefety', 'user'])->get();
        return $this->success([
            'cars' => $cars
        ], 'Cars fetched successfully', 200);
    }

    public function update(Request $request, $id)
    {
        $car = Car::with([
            'car_detail',
            'multi_images',
            'char_internal',
            'char_external',
            'char_comfort',
            'char_sefety'
        ])->findOrFail($id);

        // ✅ Update car main table
        $car->update($request->only([
            'user_id',
            'title',
            'slug',
            'model',
            'brand_name',
            'description',
            'location',
            'date',
            'price',
            'image'
        ]));

        // ✅ Update car_detail
        if ($car->car_detail) {
            $car->car_detail->update($request->only([
                'body_type',
                'condition',
                'year',
                'cylinders',
                'mileage',
                'transmission',
                'displacement',
                'color',
                'fuel_type',
                'drive_type',
                'doors',
                'vin'
            ]));
        }

        // ✅ Update char_internal
        if ($car->char_internal) {
            $car->char_internal->update([
                'name' => $request->char_internal_name
            ]);
        }

        // ✅ Update char_external
        if ($car->char_external) {
            $car->char_external->update([
                'name' => $request->char_external_name
            ]);
        }

        // ✅ Update char_comfort
        if ($car->char_comfort) {
            $car->char_comfort->update([
                'name' => $request->char_comfort_name
            ]);
        }

        // ✅ Update char_sefety
        if ($car->char_sefety) {
            $car->char_sefety->update([
                'name' => $request->char_sefety_name
            ]);
        }

        // ✅ Handle multi_images update
        if ($request->hasFile('multi_images')) {
            $paths = [];
            foreach ($request->file('multi_images') as $img) {
                $imageName = time() . '_' . uniqid() . '.' . $img->extension();
                $img->move(public_path('uploads/multi_images'), $imageName);
                $paths[] = 'uploads/multi_images/' . $imageName;
            }

            if ($car->multi_images) {
                $car->multi_images->update([
                    'images' => json_encode($paths)
                ]);
            } else {
                $car->multi_images()->create([
                    'images' => json_encode($paths)
                ]);
            }
        }

        return $this->success(
            ['car' => $car->fresh([
                'car_detail',
                'multi_images',
                'char_internal',
                'char_external',
                'char_comfort',
                'char_sefety'
            ])],
            'Car updated successfully',
            200
        );
    }

public function destroy($id)
{
    $car = Car::with([
        'car_detail',
        'multi_images',
        'char_internal',
        'char_external',
        'char_comfort',
        'char_sefety'
    ])->find($id);

    // delete relations first
    $car->car_detail()->delete();
    $car->multi_images()->delete();
    $car->char_internal()->delete();
    $car->char_external()->delete();
    $car->char_comfort()->delete();
    $car->char_sefety()->delete();

    // delete the car itself
    $car->delete();

    return $this->success(['car' => $car], 'Car and related data deleted successfully', 200);
}




}
