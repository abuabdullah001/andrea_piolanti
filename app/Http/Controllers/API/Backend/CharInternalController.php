<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use App\Models\Char_internal;

class CharInternalController extends Controller
{
    use apiresponse;

   public function index()
    {
        $char_internal = Char_internal::with('car')->get();
        return $this->success([
            'char_internal' => $char_internal
        ], 'Char internal fetched successfully', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|integer|exists:cars,id',
            'name'   => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        $char_internal = new Char_internal;
        $char_internal->car_id = $request->car_id;
        $char_internal->name   = $request->name;
        $char_internal->save();

        return $this->success([
            'char_internal' => $char_internal
        ], 'Char internal created successfully', 200);
    }

    public function update(Request $request, $id)
    {
        $char_internal = Char_internal::find($id);

        if (!$char_internal) {
            return $this->error([], 'Char internal not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'car_id' => 'sometimes|integer|exists:cars,id',
            'name'   => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        if ($request->has('car_id')) {
            $char_internal->car_id = $request->car_id;
        }

        if ($request->has('name')) {
            $char_internal->name = $request->name;
        }

        $char_internal->save();

        return $this->success([
            'char_internal' => $char_internal,
        ], 'Char internal updated successfully', 200);
    }

    public function destroy($id)
    {
        $char_internal = Char_internal::find($id);

        if (!$char_internal) {
            return $this->error([], 'Char internal not found', 404);
        }

        $char_internal->delete();

        return $this->success([
            'char_internal' => $char_internal,
        ], 'Char internal deleted successfully', 200);
    }

    
}
