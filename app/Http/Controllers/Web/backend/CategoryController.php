<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helper\Helper;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function get(Request $request)
    {
        $query = Category::query();

        if (!empty($request->id) && empty($request->name) && empty($request->priority)) {
            $query->where('id', $request->id);
        }
        if (!empty($request->name)) {
            if (!empty($request->id)) {
                $query->where('id', '!=', $request->id)->where('name', $request->name);
            }
            $query->where('name', $request->name);
        }
        if (!empty($request->priority)) {
            if (!empty($request->id)) {
                $query->where('id', '!=', $request->id)->where('priority', $request->priority);
            }
            $query->where('priority', $request->priority);
        }

        $categories = $query->get();

        return response()->json($categories);
    }

    public function priority(Request $request)
    {
        try {
            foreach ($request->ranks as $rankData) {
                $category = Category::find($rankData['id']);
                if ($category) {
                    $category->priority = $rankData['rank'];
                    $category->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Priority updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update priority', 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request)
    {
        $cate = Category::find($request->id);


        if ($cate->status == 'active') {
            $cate->update([
                'status' => 'inactive',
            ]);
        } else {
            $cate->update([
                'status' => 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status Updated'
        ]);
    }

    public function destroy($id)
    {
        $delete = Category::find($id)->update([
            'priority' => 0
        ]);

        $delete = Category::find($id)->delete();
        if ($delete) {
            return back()->with('success', 'Deleted Successfully');
        } else {
            return back()->with('error', 'Try Again!');
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    return '<img src="' . asset($data->image) . '" width="24" alt="">';
                })
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="statusCategory(' . $data->id . ')" type="checkbox" ' . ($data->status == 'active' ? 'checked' : '') . '>
                            </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<button onclick="editCategory(' . $data->id . ')" type="button" class="btn btn-info">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button"  onclick="deleteData(\'' . route('category.destroy', $data->id) . '\')" class="btn btn-danger del">
                                <i class="mdi mdi-delete"></i>
                            </button>';
                })
                ->setRowAttr([
                    'data-id' => function ($data) {
                        return $data->id;
                    }
                ])
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layout.category.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // If Category Exists In Trash Restore IT
        $category = Category::onlyTrashed()->where('name', $request->name)->first();
        if ($category) {
            $category->restore();
            $highestPriority = Category::max('priority');
            $priority = $highestPriority ? $highestPriority + 1 : 1;
            $category->priority = $priority;
            $category->status = 'active';
            $category->image = 'default.jpg';
            $category->save();
            return back()->with('success', 'Category successfully restored');
        }
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255|unique:categories,name',
            'priority' => 'nullable|numeric|unique:categories,priority',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'The category name is required.',
            'name.string'   => 'The category name must be a valid string.',
            'name.max'      => 'The category name cannot exceed 255 characters.',
            'name.unique'   => 'This category name is already taken. Please choose a different name.',

            'priority.numeric'  => 'The priority must be a number.',
            'priority.unique'   => 'This priority value is already taken. Please choose a different one.',

            'image.required'    => 'An image is required for the category.',
            'image.image'       => 'The file must be an image.',
            'image.mimes'       => 'Only JPEG, PNG, JPG, and GIF formats are allowed.',
            'image.max'         => 'The image size must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput();
        }

        try {
            // Auto-assign priority if not provided
            if (empty($data['priority'])) {
                $highestPriority = Category::max('priority');
                $data['priority'] = $highestPriority ? $highestPriority + 1 : 1;
            }

            if ($request->hasFile('image')) {
                $data['image'] = Helper::fileUpload($request->image, 'categories', $request->name . "-" . time());
            }

            $data['slug'] = Str::slug($request->name);
            $category = Category::create($data);

            return back()->with('success', 'Category successfully created');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($request->id),
            ],
            'priority' => [
                'required',
                'numeric',
                Rule::unique('categories', 'priority')->ignore($request->id),
            ],
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'The category name is required.',
            'name.string'   => 'The category name must be a valid string.',
            'name.max'      => 'The category name cannot exceed 255 characters.',
            'name.unique'   => 'This category name is already taken. Please choose a different name.',

            'priority.required' => 'The priority field is required.',
            'priority.numeric'  => 'The priority must be a number.',
            'priority.unique'   => 'This priority value is already taken. Please choose a different one.',

            'image.image'       => 'The file must be an image.',
            'image.mimes'       => 'Only JPEG, PNG, JPG, and GIF formats are allowed.',
            'image.max'         => 'The image size must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            // return response()->json(['errors' => $validator->errors()], 422);
            return back()->with('error', $validator->errors()->first())->withInput();
        }
        $category = Category::find($request->id);
        try {
            if ($request->hasFile('image')) {
                if (file_exists($category->image) && $category->image != 'default.jpg') {
                    unlink($category->image);
                }
                $data['image'] = Helper::fileUpload($request->image, 'categories', $request->name . "-" . time());
            }
            $data['slug'] = $category->name != $request->name ? Str::slug($request->name) : $category->slug;
            $category = $category->update($data);
            // return response()->json(['message' => 'category created successfully!', 'service' => $service], 201);
            return back()->with('success', 'category successfully created');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
