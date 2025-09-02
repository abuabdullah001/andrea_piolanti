<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;

class ItemController extends Controller
{
    use apiresponse;
    //=======================================
    // Business Owner Methods
    //=======================================
    public function get(Request $request){
        $data = Item::where('customer_id', $request->customer_id)->where('owner_id', Auth::id())->latest()->get();
        $data->subtotal = $data->sum('price');
        $data = $data->map(function ($d) {
            unset($d->created_at, $d->updated_at, $d->deleted_at);
            return $d;
        });
        return $this->success($data, 'Item added successfully', 201);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'customer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        Item::create([
            'description' => $request->description,
            'price'       => $request->price,
            'customer_id' => $request->customer_id,
            'owner_id'    => Auth::id(),
        ]);

        $data = Item::where('customer_id', $request->customer_id)->where('owner_id', Auth::id())->latest()->get();

        return $this->success($data, 'Item added successfully', 201);
    }

    public function remove($id){
        $item = Item::find($id);
        if (!$item) {
            return $this->error([], 'Item not found', 404);
        }
        $item->delete();
        return $this->success([], 'Item deleted successfully', 200);
    }
    //=======================================
    // Business Owner Methods
    //=======================================

    //==========================================================================================================

    //=======================================
    // Customer Methods
    //=======================================
}
