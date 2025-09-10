<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use App\Models\Multi_image;



class MultiImageController extends Controller
{
    use apiresponse;

    /**
     * Get all images
     */
    public function index()
    {
        $images = Multi_image::all();
        return $this->success($images);
    }

    /**
     * Store multiple images for a car
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_id'   => 'required|exists:cars,id',
            'images'   => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $imageName = time() . '_' . uniqid() . '.' . $img->extension();
                $img->move(public_path('uploads/multi_images'), $imageName);

                $paths[] = 'uploads/multi_images/' . $imageName;
            }
        }

        $multiImage = new Multi_image();
        $multiImage->car_id = $request->car_id;
        $multiImage->images = json_encode($paths); // ✅ save all in one row
        $multiImage->save();

        return $this->success($multiImage, 'Images uploaded successfully');
    }


    /**
     * Update a single image row
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'images'   => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $multiImage = Multi_image::find($id);
        if (!$multiImage) {
            return $this->error([], 'Record not found', 404);
        }

        // delete old files
        $oldImages = json_decode($multiImage->images, true) ?? [];
        foreach ($oldImages as $old) {
            if (file_exists(public_path($old))) {
                unlink(public_path($old));
            }
        }

        $paths = [];
        foreach ($request->file('images') as $img) {
            $imageName = time() . '_' . uniqid() . '.' . $img->extension();
            $img->move(public_path('uploads/multi_images'), $imageName);

            $paths[] = 'uploads/multi_images/' . $imageName;
        }

        $multiImage->images = json_encode($paths);
        $multiImage->save();

        return $this->success($multiImage, 'Images updated successfully');
    }


    /**
     * Delete a single image row
     */
    public function destroy($id)
    {
        $multiImage = Multi_image::find($id);

        if (!$multiImage) {
            return $this->error([], 'Record not found', 404);
        }

        $images = json_decode($multiImage->images, true) ?? [];
        foreach ($images as $file) {
            if (file_exists(public_path($file))) {
                unlink(public_path($file));
            }
        }

        $multiImage->delete();

        return $this->success([], 'Images deleted successfully');
    }
}
