<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use apiresponse;
    public function get(Request $request)
    {
        $query = Category::query();

        $categories = $query->where('status', 'active')->select('id', 'name', 'image', 'car_id')->get();
        $categories->map(function ($cate) {
            $cate->image = asset($cate->image);
            return $cate;
        });

        return $this->success($categories, 'Successfully!', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->slug = str_replace(' ', '-', strtolower($request->name));
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $category->image = 'uploads/categories/' . $imageName; // save path in DB
        }
        $category->car_id = $request->car_id;
        $category->save();

        return $this->success($category, 'Category created successfully', 200);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $category = Category::find($id);

        if (!$category) {
            return $this->error([], 'Category not found', 404);
        }

        $category->name = $request->name;
         if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $category->image = 'uploads/categories/' . $imageName; // save path in DB
        }
        $category->car_id = $request->car_id;
        $category->save();

        return $this->success($category, 'Category updated successfully', 200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->error([], 'Category not found', 404);
        }

        $category->delete();

        return $this->success([], 'Category deleted successfully', 200);
    }
}
