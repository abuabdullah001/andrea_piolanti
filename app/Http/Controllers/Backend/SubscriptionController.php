<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\SubscriptionPlan;
use App\Models\UserFreeTrial;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function plans()
    {
        $data['subscriptions'] = SubscriptionPlan::with('pricingOptions', 'features')->get();
        return view('backend.subscription.plans', $data);
    }
    public function edit($id)
    {
        $data['plan'] = SubscriptionPlan::with('pricingOptions', 'features')->find($id);
        $data['features'] = Feature::all();
        return view('backend.subscription.edit', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'              => 'required|exists:subscription_plans,id',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:1000',
            'features'        => 'nullable|array',
            'features.*'      => 'exists:features,id',
            // 'price'           => 'array',
            // 'price.*'         => 'nullable|numeric|min:0',
            // 'discount_note'   => 'array',
            // 'discount_note.*' => 'nullable|string|max:255',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->id);
        $plan->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        // Update pricing options
        // $prices = $request->input('price', []);
        // $notes  = $request->input('discount_note', []);

        // foreach ($plan->pricingOptions as $pricingOption) {
        //     $id = $pricingOption->id;
        //     if (isset($prices[$id])) {
        //         $pricingOption->price = $prices[$id];
        //         $pricingOption->discount_note = $notes[$id] ?? null;
        //         $pricingOption->save();
        //     }
        // }


        // Sync features (many-to-many)
        $plan->features()->sync($request->features ?? []);

        return redirect()->back()->with('success', 'Subscription plan updated successfully.');
    }

    public function requests()
    {
        return view('backend.subscription.requests');
    }
    public function freeTrial()
    {
        $users = UserFreeTrial::with('user', 'subscriptionPlan')->get();
        return view('backend.subscription.freeTrial', compact('users'));
    }
    public function activeUsers()
    {
        $users = UserSubscription::with([
            'user',
            'subscriptionPricingOption.subscriptionPlan'
        ])->get();
        return view('backend.subscription.activeUsers', compact('users'));
    }

    public function expiredUsers()
    {
        $users = UserSubscription::with([
            'user',
            'subscriptionPricingOption.subscriptionPlan'
        ])->where('end_date', '<', now())->get();
        return view('backend.subscription.expiredUsers', compact('users'));
    }

    public function status(Request $request)
    {
        $cate = SubscriptionPlan::find($request->id);


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
}
