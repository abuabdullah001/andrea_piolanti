<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    use apiresponse;
    public function index(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0.01',
            'product_quantity' => 'required|integer|min:1',
        ]);
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $validated['product_name']],
                    'unit_amount' => $validated['product_price'] * 100,
                ],
                'price' => $validated['product_price'] * 100,
                'quantity' => $validated['product_quantity'],
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.api.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.api.cancel'),
            'metadata' => [
                'product_name' => $validated['product_name'],
                'product_price' => $validated['product_price'],
                'product_quantity' => $validated['product_quantity'],
            ]
        ]);
        $data = [
            'checkout_url' => $session->url
        ];
        return $this->success($data, 'Redirect to checkout successfully.', 200);
    }
    public function paymentSuccess(Request $request)
    {
        if ($request->has('session_id')) {
            $stripe   = new StripeClient(env('STRIPE_SK'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);
            $metadata = $response->metadata->toArray();
            $data     = [
                'payment_id'       => $response->id,
                'product_name'     => $metadata['product_name'],
                'product_price'    => $metadata['product_price'],
                'product_quantity' => $metadata['product_quantity'],
                'customer_name'    => $response->customer_details->name,
                'customer_email'   => $response->customer_details->email,
                'payment_status'   => $response->payment_status,
                'currency'         => $response->currency,
                'payment_method'   => 'stripe',
            ];
            return $this->success($data, 'Payment was successfull.', 200);
        } else {
            return $this->error([], 'Payment was cancelled.', 400);
        }
    }
    public function cancel()
    {
        return $this->error([], 'Payment was cancelled.', 400);
    }
}
