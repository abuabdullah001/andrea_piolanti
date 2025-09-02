<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserPaymentMethodInfo;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentMethodInfo extends Controller
{
    use apiresponse;

    //=======================================
    // Business Owner Methods
    //=======================================

    // get
    public function get(Request $request)
    {
        $query = UserPaymentMethodInfo::query();

        $query->where('user_id', Auth::id());

        if (!empty($request->type)) {
            $query->where('type', $request->type);
        }

        $query->select('id', 'type', 'card_holder_name', 'card_number', 'expiry_date', 'cvv');

        $rslt = $query->first();

        if ($rslt) {
            return $this->success($rslt, 'Payment Method Found', 200);
        } else {
            return $this->error([], 'Payment Method Not Found', 404);
        }
    }

    // store
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'type'             => 'required',
            'card_holder_name' => 'required',
            'card_number'      => 'required',
            'expiry_date'      => 'required',
            'cvv'              => 'required',
        ]);

        if ($validation->fails()) {
            return $this->error($validation->errors(), 'Validation Error', 422);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $rslt = UserPaymentMethodInfo::create($data);
        if($rslt){
            return $this->success([], 'Payment Method Added Successfully', 200);
        }else{
            return $this->error([], 'Payment Method Not Added', 500);
        }
    }

    // Remove
    public function remove($id)
    {
        $rslt = UserPaymentMethodInfo::where('id', $id)->delete();
        if($rslt){
            return $this->success([], 'Payment Method Removed Successfully', 200);
        }else{
            return $this->error([], 'Payment Method Not Removed', 500);
        }
    }

    //=======================================
    // Business Owner Methods
    //=======================================
}
