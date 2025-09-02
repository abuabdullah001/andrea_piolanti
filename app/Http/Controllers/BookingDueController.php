<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingDueRequest;
use App\Models\Service;
use App\Models\User;
use App\Notifications\BookingNotifications;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;

class BookingDueController extends Controller
{
    use apiresponse;

    //=======================================
    // Business Owner Methods
    //=======================================
    public function getDueList()
    {
        $booking = Booking::where('owner_id', Auth::id())
            ->where('due', '>', 0)
            ->get();

        $data = $booking->map(function ($item) {
            $alreadyRequested = BookingDueRequest::where('booking_id', $item->id)->exists();

            return [
                'id'                => $item->id,
                'invoice_no'        => $item->invoice_no,
                'date'              => $item->date,
                'subtotal'          => $item->subtotal,
                'discount'          => $item->discount,
                'tax'               => $item->tax,
                'total'             => $item->total,
                'due'               => $item->due,
                'advance'           => $item->advance,
                'status'            => $item->status,
                'service'           => $item->service->title,
                'service_at'        => $item->service->service_at,
                'timeSlot'          => $item->timeSlot->time,
                'customer'          => $item->customer->name,
                'already_requested' => $alreadyRequested ? 'Yes' : 'No',
            ];
        });

        return $this->success($data, 'Booking Due List', 200);
    }
    public function duePaymentRequest(Request $request)
    {
        $booking  = Booking::find($request->booking_id);
        $customer = User::find($booking->customer_id);
        $service  = Service::find($booking->service_id);

        $requested_amount = $request->requested_amount;
        $full_due_amount  = $booking->due;

        if ($requested_amount > $full_due_amount) {
            return $this->error([], 'Requested amount cannot exceed due amount', 422);
        }

        BookingDueRequest::create([
            'booking_id'               => $request->booking_id,
            'owner_id'                 => Auth::id(),
            'customer_id'              => $customer->id,
            'service_id'               => $service->id,
            'requested_payable_amount' => $requested_amount,
            'full_payable_amount'      => $full_due_amount,
            'note'                     => $request->note,
            'requested_at'             => Carbon::now(),
        ]);

        Notification::send($customer, new BookingNotifications('Booking Due Payment Request', 'Business owner requested to pay due payment', $service->title, Carbon::now()));

        return $this->success([], 'Booking Due Payment Request', 200);
    }

    public function getDuePaymentRequestList()
    {
        $booking = BookingDueRequest::where('owner_id', Auth::id())->get();

        $data = $booking->map(function ($item) {
            return [
                'id'                  => $item->id,
                'invoice_no'          => $item->booking->invoice_no,
                'date'                => $item->booking->date,
                'total'               => $item->booking->total,
                'service'             => $item->service->title,
                'service_at'          => $item->service->service_at,
                'customer'            => $item->customer->name,
                'requested_amount'    => $item->requested_payable_amount,
                'full_payable_amount' => $item->full_payable_amount,
                'note'                => $item->note,
                'requested_at'        => $item->requested_at,
            ];
        });
        return $this->success($data, 'Booking Due Payment Request List', 200);
    }

    public function duePaymentRequestAgain($id)
    {
        $booking = BookingDueRequest::find($id);

        $booking->update([
            'requested_at' => Carbon::now(),
        ]);

        $customer = User::find($booking->customer_id);

        Notification::send($customer, new BookingNotifications('Booking Due Payment Request', 'Business owner requested to pay due payment', $booking->service->title, Carbon::now()));

        return $this->success([], 'Booking Due Payment Request Again', 200);
    }

    public function cancelDuePaymentRequest($id)
    {
        $request = BookingDueRequest::find($id);
        $request->delete();
        return $this->success([], 'Booking Due Payment Request Cancelled', 200);
    }

    public function duePaymentCustom(Request $request){
        $validate = Validator::make($request->all(), [
            'booking_id' => 'required',
            'amount'     => 'required',
        ]);

        if ($validate->fails()) {
            return $this->error($validate->errors(), 'Validation error', 422);
        }
        $booking = Booking::find($request->booking_id);
        if(!$booking){
            return $this->error([], 'Booking not found', 404);
        }
        if($request->amount > $booking->due){
            return $this->error([], 'Amount cannot exceed due amount', 422);
        }
        $booking->advance += $request->amount;
        $booking->due -= $request->amount;
        $booking->save();
        return $this->success([], 'Due Payment Added Successfully', 200);
    }
    //=======================================
    // Business Owner Methods
    //=======================================

    //==========================================================================================================

    //=======================================
    // Customer Methods
    //=======================================

    public function getPaymentRequests()
    {
        $booking = BookingDueRequest::where('customer_id', Auth::id())->get();

        $data = $booking->map(function ($item) {
            return [
                'id'                  => $item->id,
                'invoice_no'          => $item->booking->invoice_no,
                'date'                => $item->booking->date,
                'total'               => $item->booking->total,
                'service'             => $item->service->title,
                'service_at'          => $item->service->service_at,
                'customer'            => $item->customer->name,
                'requested_amount'    => $item->requested_payable_amount,
                'full_payable_amount' => $item->full_payable_amount,
                'note'                => $item->note,
                'requested_at'        => $item->requested_at,
            ];
        });

        return $this->success($data, 'Booking Due Payment Request List', 200);
    }

    public function paymentRequestDetails($id)
    {
        $booking = BookingDueRequest::with(['booking', 'booking.service', 'customer'])->find($id);


        if(!$booking){
            return $this->error([], 'Invalid Request', 404);
        }

        if($booking->customer_id != Auth::id()) {
            return $this->error([], 'You are not allowed to view this request', 403);
        }

        $data = [
            'id'                  => $booking->id,
            'invoice_no'          => $booking->booking->invoice_no,
            'date'                => $booking->booking->date,
            'total'               => $booking->booking->total,
            'service'             => $booking->booking->service->title,
            'service_at'          => $booking->booking->service->service_at,
            'customer'            => $booking->customer->name,
            'requested_amount'    => $booking->requested_payable_amount,
            'full_payable_amount' => $booking->full_payable_amount,
            'note'                => $booking->note,
            'requested_at'        => $booking->requested_at,
        ];

        return $this->success($data, 'Booking Due Payment Request Details', 200);
    }

    public function payBooking(Request $request)
    {
        $booking = BookingDueRequest::find($request->due_id);

        if($booking->customer_id != Auth::id()) {
            return $this->error([], 'You are not allowed to pay this request', 403);
        }
        if($request->type == 'fullpayment') {
            $paymentAmount = $booking->full_payable_amount;
        } else {
            $paymentAmount = $booking->requested_payable_amount;
        }
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => $booking->service->title],
                    'unit_amount'  => $paymentAmount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.duePayment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('stripe.duePayment.cancel'),
            'metadata'    => [
                'booking_due_request_id' => $booking->id,
                'paymentAmount'          => $paymentAmount
            ]
        ]);

        return $this->success(['checkout_url' => $session->url], 'Redirect to checkout successfully.', 200);
    }

    public function payBookingSuccessfull(Request $request)
    {
        $session_id = $request->session_id;
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve($session_id, []);
        $bookingDueRequest = BookingDueRequest::find($session->metadata->booking_due_request_id);
        $paymentAmount = $session->metadata->paymentAmount;
        $booking = $bookingDueRequest->booking;

        $booking->update([
            'due'     => $booking->due - $paymentAmount,
            'advance' => $booking->advance + $paymentAmount,
        ]);

        $bookingDueRequest->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        return $this->success([], 'Booking Due Payment Successfull', 200);
    }

    public function payBookingCancelled(Request $request)
    {
        return $this->success([], 'Booking Due Payment Cancelled', 200);
    }
}
