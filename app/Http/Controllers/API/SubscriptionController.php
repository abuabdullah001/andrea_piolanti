<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPricingOption;
use App\Models\UserFreeTrial;
use App\Models\UserSubscription;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    use apiresponse;

    //=======================================
    // Business Owner Methods
    //=======================================

    // Plans
    public function plans()
    {
        $subscriptions = SubscriptionPlan::where('status', 'active')
            ->select('id', 'name', 'description')
            ->with([
                'pricingOptions:id,subscription_plan_id,billing_period,price,duration_days,discount_note',
                'features:id,name,description'
            ])
            ->get();

        $userFreeTrials = UserFreeTrial::where('user_id', Auth::id())
            ->pluck('subscription_plan_id')
            ->toArray();

        $subscriptions = $subscriptions->map(function ($plan) use ($userFreeTrials) {
            $plan->free_trial = in_array($plan->id, $userFreeTrials) ? 'disabled' : 'enabled';
            return $plan;
        });

        return $this->success($subscriptions, 'Plans fetched successfully', 200);
    }


    public function userPurchasePlans(Request $request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET')); // Stripe Client
        $user = Auth::user(); // Get Authenticated User

        // Validate Request
        $validator = Validator::make($request->all(), [
            'subscription_pricing_option_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }
        // Validate Request

        $subsprice = SubscriptionPricingOption::find($request->subscription_pricing_option_id); // Get Subscription Pricing Option
        $plan = $subsprice->subscriptionPlan; // Get Subscription Plan

        // Check If User Already Have A Free Trial
        if (UserFreeTrial::where('user_id', $user->id)->where('subscription_plan_id', $plan->id)->whereDate('end_date', '>=', now())->exists()) {
            return $this->error([], 'You already have a free trial', 400);
        }

        // Check If User Already Have A Plan
        if (UserSubscription::where('user_id', $user->id)->where('subscription_pricing_option_id', $subsprice->id)->whereDate('end_date', '>=', now())->exists()) {
            return $this->error([], 'You already have a plan', 400);
        }


        if (!$subsprice || !$subsprice->stripe_price_id) {
            return $this->error([], 'Stripe price ID missing', 400);
        }

        $checkIfUserHasFreeTrial = UserFreeTrial::where('user_id', $user->id)->where('subscription_plan_id', $plan->id)->exists();

        $subscriptionData = [];

        if (!$checkIfUserHasFreeTrial) {

            UserFreeTrial::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $plan->id,
                'start_date'           => now(),
                'end_date'             => now()->addDays(7),
            ]);

            $subscriptionData['trial_period_days'] = 7;
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price'    => $subsprice->stripe_price_id,
                'quantity' => 1,
            ]],
            'mode'              => 'subscription',
            'subscription_data' => $subscriptionData,
            'success_url'       => route('subscription.purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'        => route('subscription.purchase.cancel'),
            'metadata'          => [
                'user_id'      => $user->id,
                'subsprice_id' => $subsprice->id,
            ],
        ]);

        return $this->success([
            'checkout_url' => $checkout_session->url
        ], 'Redirect to checkout successfully.', 200);
    }

    public function userPurchasePlansSuccess(Request $request)
    {
        $stripe  = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve($request->session_id);

        $user_id      = $session->metadata->user_id;
        $subsprice_id = $session->metadata->subsprice_id;

        $subsprice = SubscriptionPricingOption::find($subsprice_id);

        // Check if user already has an active or expired subscription with same plan
        $subscription = UserSubscription::where('user_id', $user_id)
            ->where('subscription_pricing_option_id', $subsprice_id)
            ->first();

        $startDate = now();
        $endDate = $startDate->copy()->addDays($subsprice->duration_days);

        if ($subscription) {
            // Update existing subscription
            $subscription->update([
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ]);
        } else {
            // Create new subscription
            UserSubscription::create([
                'user_id'                        => $user_id,
                'subscription_pricing_option_id' => $subsprice_id,
                'start_date'                     => $startDate,
                'end_date'                       => $endDate,
            ]);
        }

        return $this->success([], 'Plan Purchase Successful', 200);
    }

    public function userPurchasePlansCancel()
    {
        return $this->error([], 'Payment was cancelled.', 400);
    }

    //=======================================
    // Business Owner Methods
    //=======================================


    //==========================================================================================================

    //=======================================
    // Customer Methods
    //=======================================
}
