<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class PromoController extends Controller
{
    public function get(Request $request)
    {
        $query = Promo::query();

        if (!empty($request->id) && empty($request->code)) {
            $query->where('id', $request->id);
        }
        if (!empty($request->code)) {
            if (!empty($request->id)) {
                $query->where('id', '!=', $request->id)->where('promo_code', $request->code);
            }
            $query->where('promo_code', $request->code);
        }

        $promos = $query->get();

        return response()->json($promos);
    }

    public function status(Request $request)
    {
        $prom = Promo::find($request->id);


        if ($prom->status == 'active') {
            $prom->update([
                'status' => 'inactive',
            ]);
        } else {
            $prom->update([
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
        $delete = Promo::find($id)->delete();
        if ($delete) {
            return back()->with('success', 'Deleted Successfully');
        } else {
            return back()->with('error', 'Try Again!');
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Promo::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('code', function ($data) {
                    return '<span class="badge bg-primary">' . $data->promo_code . '</span>';
                })
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="toggleStatus(' . $data->id . ')" type="checkbox" ' . ($data->status == 'active' ? 'checked' : '') . '>
                            </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<button onclick="editPromo(' . $data->id . ')" type="button" class="btn btn-info btn-sm">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button type="button" onclick="deleteData(\'' . route('promo.destroy', $data->id) . '\')" class="btn btn-danger btn-sm">
                                <i class="mdi mdi-delete"></i>
                            </button>';
                })
                ->setRowAttr([
                    'data-id' => function ($data) {
                        return $data->id;
                    }
                ])
                ->rawColumns(['code', 'status', 'action'])
                ->make(true);
        }

        $customers = User::role('user')->get();

        return view('backend.layout.promo.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'promo_code' => 'required|string|unique:promos,promo_code|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ], [
            'user_id.required' => 'The shop field is required.',
            'user_id.exists' => 'The selected shop does not exist.',
            'promo_code.required' => 'The promo code field is required.',
            'promo_code.string' => 'The promo code must be a valid string.',
            'promo_code.unique' => 'This promo code is already taken.',
            'promo_code.max' => 'The promo code may not be greater than 255 characters.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a valid string.',
            'description.max' => 'The description may not be greater than 500 characters.',
            'discount_type.required' => 'The discount type field is required.',
            'discount_type.in' => 'The discount type must be either Fixed or Percentage.',
            'discount_value.required' => 'The discount value field is required.',
            'discount_value.numeric' => 'The discount value must be a number.',
            'discount_value.min' => 'The discount value must be at least 1.',
            'start_date.date' => 'The start date must be a valid date.',
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after' => 'The end date must be after the start date.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput();
        }

        try {
            $promo = Promo::create($data);
            return back()->with('success', 'promo successfully created');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'promo_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('promos', 'promo_code')->ignore($request->id),
            ],
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ], [
            'user_id.required' => 'The shop field is required.',
            'user_id.exists' => 'The selected shop does not exist.',
            'promo_code.required' => 'The promo code field is required.',
            'promo_code.string' => 'The promo code must be a valid string.',
            'promo_code.unique' => 'This promo code is already taken.',
            'promo_code.max' => 'The promo code may not be greater than 255 characters.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a valid string.',
            'description.max' => 'The description may not be greater than 500 characters.',
            'discount_type.required' => 'The discount type field is required.',
            'discount_type.in' => 'The discount type must be either Fixed or Percentage.',
            'discount_value.required' => 'The discount value field is required.',
            'discount_value.numeric' => 'The discount value must be a number.',
            'discount_value.min' => 'The discount value must be at least 1.',
            'start_date.date' => 'The start date must be a valid date.',
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after' => 'The end date must be after the start date.',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput();
        }
        $promo = Promo::find($request->id);
        try {

            $promo = $promo->update($data);
            return back()->with('success', 'Promo Updated');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
