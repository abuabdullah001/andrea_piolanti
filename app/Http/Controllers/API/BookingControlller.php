<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingChangeRequest;
use App\Models\Item;
use App\Models\Service;
use App\Models\User;
use App\Notifications\BookingNotifications;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BookingResource;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

class BookingControlller extends Controller
{
    use apiresponse;

    //=======================================
    // Business Owner Methods
    //=======================================

    public function getOwnerBookings(Request $request)
    {
        $query = Booking::query();

        $query->where('owner_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_filter')) {
            $now = now();

            if ($request->date_filter == 'today') {
                $query->whereDate('date', $now->toDateString());
            } elseif ($request->date_filter == 'week') {
                $query->whereBetween('date', [
                    $now->toDateString(),
                    $now->copy()->addDays(6)->toDateString()
                ]);
            } elseif ($request->date_filter == 'month') {
                $query->whereBetween('date', [
                    $now->toDateString(),
                    $now->copy()->addMonth()->toDateString()
                ]);
            }
        } else {
            // By default: bookings from today onwards
            $query->whereDate('date', '>=', now()->toDateString());
        }
        // Total Booking
        $total_booking = $query->count();

        $query->with(['service', 'customer', 'owner', 'timeSlot', 'items']);

        $bookings = $query->latest()->get();

        $booking_data = $bookings->map(function ($item) {
            return [
                'id'             => $item->id,
                'date'           => $item->date,
                'time'           => $item->timeSlot->time,
                'subtotal'       => $item->subtotal,
                'total'          => $item->total,
                'advance'        => $item->advance,
                'due'            => $item->due,
                'payment_status' => $item->payment_status,
                'payment_method' => $item->payment_method,
                'status'         => $item->status,
                'service'        => $item->service ? [
                    'id'       => $item->service->id,
                    'title'    => $item->service->title,
                    'slug'     => $item->service->slug,
                    'price'    => $item->service->price,
                    'image'    => asset($item->service->image),
                    'duration' => $item->service->duration,
                    'location' => $item->service->location,
                ] : null,
                'customer'       => $item->customer ? [
                    'id'       => $item->customer->id,
                    'name'     => $item->customer->name,
                    'username' => $item->customer->username,
                    'email'    => $item->customer->email,
                    'avatar'   => asset($item->customer->avatar),
                ] : null,
                'owner'          => $item->owner ? [
                    'id'       => $item->owner->id,
                    'name'     => $item->owner->name,
                    'username' => $item->owner->username,
                    'email'    => $item->owner->email,
                    'avatar'   => asset($item->owner->avatar),
                ] : null,
                'items'          => $item->items->map(function ($i) {
                    return [
                        'id'          => $i->id,
                        'description' => $i->description,
                        'price'       => $i->price,
                    ];
                }),
            ];
        });

        $data = [
            'total_booking' => $total_booking,
            'bookings' => $booking_data
        ];

        return $this->success($data, 'Booking data fetched successfully');
    }

    public function changeRequest(Request $request)
    {
        $data = BookingChangeRequest::query()
            ->whereHas('booking', function ($q) {
                $q->where('owner_id', Auth::id());
            });

        if ($request->filled('booking_id')) {
            $data->where('booking_id', $request->booking_id);
        }

        if ($request->filled('customer_id')) {
            $data->where('customer_id', $request->customer_id);
        }

        if ($request->filled('type')) {
            $data->where('type', $request->type);
        }

        $data = $data->with(['booking', 'customer', 'respondedBy', 'timeSlot'])->latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No booking change requests found', 404);
        }

        // Manual data transformation
        $transformed = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'booking_id' => $item->booking_id,
                'customer_id' => $item->customer_id,
                'type' => $item->type,
                'requested_date' => $item->requested_date,
                'requested_time_slot' => $item->requested_time_slot ? $item->timeSlot->time : null,
                'reason' => $item->reason,
                'status' => $item->status,
                'responded_by' => $item->responded_by,
                'response_note' => $item->response_note,

                'booking' => [
                    'id' => $item->booking->id ?? null,
                    'date' => $item->booking->date ?? null,
                    'service_title' => $item->booking->service ? $item->booking->service->title : null,
                    'time' => $item->booking->time_slot_id ? $item->booking->timeSlot->time : null,
                    'payment_status' => $item->booking->payment_status ?? null,
                    'status' => $item->booking->status ?? null,
                ],

                'customer' => [
                    'id' => $item->customer->id ?? null,
                    'name' => $item->customer->name ?? null,
                    'email' => $item->customer->email ?? null,
                    'avatar' => asset($item->customer->avatar),
                ],
            ];
        });

        return $this->success($transformed, 'Booking change requests');
    }

    public function ChangeRequestStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:booking_change_requests,id',
            'status' => 'required|in:approved,rejected',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation error', 422);
        }

        $requestId = $request->request_id;
        $status = $request->status;

        $bookingChangeRequest = BookingChangeRequest::find($requestId);

        $customer = User::find($bookingChangeRequest->customer_id);

        if (!$customer) {
            return $this->error([], 'Customer not found', 404);
        }

        if (!$bookingChangeRequest) {
            return $this->error([], 'Booking change request not found', 404);
        }

        $bookingChangeRequest->status = $status;
        $bookingChangeRequest->responded_by = Auth::id();
        $bookingChangeRequest->save();

        if ($request->status == 'approved') {
            if ($bookingChangeRequest->type == 'cancel') {
                $booking = Booking::find($bookingChangeRequest->booking_id);
                $booking->status = 'cancelled';
                $booking->save();
            } elseif ($bookingChangeRequest->type == 'reschedule') {
                $booking = Booking::find($bookingChangeRequest->booking_id);
                $booking->status = 'rescheduled';
                if ($bookingChangeRequest->requested_date) {
                    $booking->date = $bookingChangeRequest->requested_date;
                }
                if ($bookingChangeRequest->requested_time_slot) {
                    $booking->time_slot_id = $bookingChangeRequest->requested_time_slot;
                }
                $booking->save();
            }
            Notification::send($customer, new BookingNotifications('Booking Change Approved', 'Your booking change request has been approved', $bookingChangeRequest->booking->service->title, Carbon::now()));
        } else {
            Notification::send($customer, new BookingNotifications('Booking Change Rejected', 'Your booking change request has been rejected', $bookingChangeRequest->booking->service->title, Carbon::now()));
        }

        return $this->success([], 'Booking change request updated successfully');
    }

    public function updateChangeRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:booking_change_requests,id',
            'type' => 'required|in:cancel,reschedule',
            'requested_date' => 'required_if:type,reschedule',
            'requested_time_slot' => 'required_if:type,reschedule',
            'response_note' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation error', 422);
        }

        $requestId = $request->request_id;
        $type = $request->type;
        $requestedDate = $request->requested_date;
        $requestedTimeSlot = $request->requested_time_slot;
        $response_note = $request->response_note;

        $bookingChangeRequest = BookingChangeRequest::find($requestId);

        if (!$bookingChangeRequest) {
            return $this->error([], 'Booking change request not found', 404);
        }
        if ($type == 'reschedule') {
            $bookingChangeRequest->requested_date = $requestedDate;
            $bookingChangeRequest->requested_time_slot = $requestedTimeSlot;
        } elseif ($type == 'cancel') {
            $bookingChangeRequest->requested_date = null;
            $bookingChangeRequest->requested_time_slot = null;
        }
        $bookingChangeRequest->type = $type;
        $bookingChangeRequest->response_note = $response_note;
        $bookingChangeRequest->status = 'pending';
        $bookingChangeRequest->save();
        $customer = User::find($bookingChangeRequest->customer_id);
        if (!$customer) {
            return $this->error([], 'Customer not found', 404);
        }
        Notification::send($customer, new BookingNotifications('Booking Change Request Updated', 'Your booking change request has been updated', $bookingChangeRequest->booking->service->title, Carbon::now()));

        $owner = User::find($bookingChangeRequest->booking->owner_id);
        if ($owner) {
            Notification::send($owner, new BookingNotifications('Booking Change Request Updated', 'A booking change request has been updated', $bookingChangeRequest->booking->service->title, Carbon::now()));
        }

        return $this->success([], 'Booking change request updated successfully', 200);
    }

    public function updateBookingStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation error', 422);
        }

        $bookingId = $request->booking_id;
        $status = $request->status;

        $booking = Booking::find($bookingId);

        if (!$booking) {
            return $this->error([], 'Booking not found', 404);
        }

        $booking->status = $status;
        $booking->save();

        return $this->success([], 'Booking status updated successfully');
    }

    public function customBooking(Request $request)
    {
        if (!Auth::check()) {
            return $this->error([], 'Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'time_slot_id' => 'required|exists:service_time_slots,id',
            'customer_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'advance' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,stripe',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $serviceId = $request->service_id;
        $advance = $request->advance ?? 0;
        $service = Service::find($serviceId);

        if ($advance > $service->price) {
            return $this->error([], 'Advance cannot exceed service price', 422);
        }

        $owner = Auth::user();
        $customer = User::find($request->customer_id);

        $items = Item::where('customer_id', $request->customer_id)
            ->where('owner_id', $owner->id)
            ->get();

        if ($items->isEmpty()) {
            $subtotal = $service->price;
        } else {
            $subtotal = $service->price + $items->sum('price');
        }

        $data                   = $request->only(['service_id', 'time_slot_id', 'date', 'location', 'payment_method']);
        $data['invoice_no']    = $this->generateInvoiceNo('Booking');
        $data['customer_id']    = $request->customer_id;
        $data['owner_id']       = $owner->id;
        $data['subtotal']       = $subtotal;
        $data['total']          = $subtotal;
        $data['advance']        = $advance;
        $data['due']            = $service->price - $advance;
        $data['payment_status'] = $advance > 0 ? 'paid' : 'unpaid';

        DB::beginTransaction();
        try {
            $data['booking_type'] = 'custom';
            $booking = Booking::create($data);
            foreach ($items as $item) {
                $booking->items()->create([
                    'item_id'     => $item->id,
                    'description' => $item->description,
                    'price'       => $item->price,
                ]);
            }
            Notification::send($customer, new BookingNotifications('Booking', 'Booking created successfully', $service->title, Carbon::now()));

            // $invoiceCustomer = User::find($request->customer_id);
            // $invoiceService = Service::find($request->service_id);
            // $invoiceItems = $booking->items()->get();
            // $invoiceOwner = Auth::user();
            // $invoiceOwner->avatar = asset($invoiceOwner->avatar);

            // $invoiceData = [
            //     'booking' => $booking,
            //     'items' => $invoiceItems,
            //     'service' => $invoiceService,
            //     'customer' => $invoiceCustomer,
            //     'owner' => $invoiceOwner
            // ];
            // $senderEmail = env('MAIL_FROM_ADDRESS');
            // Mail::to($invoiceCustomer->email)->send(new InvoiceMail($invoiceData, $senderEmail));

            DB::commit();
            return $this->success($booking, 'Booking created successfully', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 'Booking failed', 500);
        }
    }

    public function bookingDetails($id)
    {
        $booking = Booking::with('customer', 'service', 'owner', 'timeSlot', 'items')->find($id);

        if ($booking) {
            $data = [
                'id'             => $booking->id,
                'customer_id'    => $booking->customer_id,
                'owner_id'       => $booking->owner_id,
                'service_id'     => $booking->service_id,
                'time_slot_id'   => $booking->time_slot_id,
                'date'           => $booking->date,
                'subtotal'       => $booking->subtotal,
                'tax'            => $booking->tax,
                'total'          => $booking->total,
                'advance'        => $booking->advance,
                'due'            => $booking->due,
                'payment_status' => $booking->payment_status,
                'payment_method' => $booking->payment_method,
                'status'         => $booking->status,
                'booking_type'   => $booking->booking_type,

                'customer' => $booking->customer ? [
                    'id'          => $booking->customer->id,
                    'name'        => $booking->customer->name,
                    'email'       => $booking->customer->email,
                    'phone'       => $booking->customer->phone,
                    'about_me'    => $booking->customer->about_me,
                    'description' => $booking->customer->description,
                    'address'     => $booking->customer->address,
                    'avatar'      => $booking->customer->avatar ? asset($booking->customer->avatar) : null,
                ] : null,

                'service' => $booking->service ? [
                    'id'          => $booking->service->id,
                    'owner_id'    => $booking->service->owner_id,
                    'title'       => $booking->service->title,
                    'slug'        => $booking->service->slug,
                    'description' => $booking->service->description,
                    'duration'    => $booking->service->duration,
                    'price'       => $booking->service->price,
                    'image'       => $booking->service->image ? asset($booking->service->image) : null,
                ] : null,

                'owner' => $booking->owner ? [
                    'id'          => $booking->owner->id,
                    'name'        => $booking->owner->name,
                    'email'       => $booking->owner->email,
                    'phone'       => $booking->owner->phone,
                    'about_me'    => $booking->owner->about_me,
                    'description' => $booking->owner->description,
                    'address'     => $booking->owner->address,
                    'avatar'      => $booking->owner->avatar ? asset($booking->owner->avatar) : null,
                ] : null,

                'time_slot' => $booking->timeSlot ? [
                    'service_id' => $booking->timeSlot->service_id,
                    'time'       => $booking->timeSlot->time,
                ] : null,

                'items' => $booking->items->map(function ($item) {
                    return [
                        'id'       => $item->id,
                        'name'     => $item->name,
                        'price'    => $item->price,
                        'quantity' => $item->quantity,
                    ];
                }),
            ];

            return $this->success($data, 'Booking details', 200);
        }

        return $this->error('Booking not found', 404);
    }

    //=======================================
    // Business Owner Methods
    //=======================================

    //==========================================================================================================

    //=======================================
    // Customer Methods
    //=======================================

    public function getCustomerBookings(Request $request)
    {
        $query = Booking::query();

        // Apply filters if present
        if ($request->filled('booking_type')) {
            $query->where('booking_type', $request->booking_type);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method); // cash / stripe
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('booking_status')) {
            $query->where('status', $request->booking_status);
        }

        $query->where('customer_id', Auth::id());

        // Load relations
        $query->with(['service', 'customer', 'owner', 'timeSlot', 'items']);

        // Optional: Paginate or get all
        $bookings = $query->latest()->get();

        $data = $bookings->map(function ($item) {
            return [
                'id'             => $item->id,
                'date'           => $item->date,
                'time'           => $item->timeSlot->time,
                'subtotal'       => $item->subtotal,
                'total'          => $item->total,
                'advance'        => $item->advance,
                'due'            => $item->due,
                'payment_status' => $item->payment_status,
                'payment_method' => $item->payment_method,
                'status'         => $item->status,
                'service'        => $item->service ? [
                    'id'       => $item->service->id,
                    'title'    => $item->service->title,
                    'slug'     => $item->service->slug,
                    'price'    => $item->service->price,
                    'image'    => asset($item->service->image),
                    'duration' => $item->service->duration,
                ] : null,
                'customer'       => $item->customer ? [
                    'id'       => $item->customer->id,
                    'name'     => $item->customer->name,
                    'username' => $item->customer->username,
                    'email'    => $item->customer->email,
                    'avatar'   => asset($item->customer->avatar),
                ] : null,
                'owner'          => $item->owner ? [
                    'id'       => $item->owner->id,
                    'name'     => $item->owner->name,
                    'username' => $item->owner->username,
                    'email'    => $item->owner->email,
                    'avatar'   => asset($item->owner->avatar),
                ] : null,
                'items'          => $item->items->map(function ($i) {
                    return [
                        'id'          => $i->id,
                        'description' => $i->description,
                        'price'       => $i->price,
                    ];
                }),
            ];
        });

        return $this->success($data, 'Booking data fetched successfully');
    }


    public function booking(Request $request)
    {

        if (!Auth::check()) {
            return $this->error([], 'Unauthorized', 401);
        }

        $validator = Validator::make($request->all(), [
            'service_id'      => 'required|exists:services,id',
            'time_slot_id'    => 'required|exists:service_time_slots,id',
            'date'            => 'required|date',
            'advance'         => 'nullable|numeric|min:0',
            'location'        => 'nullable|string|max:255',
            'payment_method'  => 'required|in:cash,stripe',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $service = Service::find($request->service_id);
        $advance = $request->advance ?? 0;

        if ($service->is_deposite == 'yes') {
            if ($advance == 0) {
                return $this->error([], 'Advance is required', 422);
            }
            if ($advance < $service->minimum_deposite) {
                return $this->error([], 'Minimum deposite is ' . $service->minimum_deposite, 422);
            }
        }

        if ($advance > $service->price) {
            return $this->error([], 'Advance cannot exceed service price', 422);
        }

        $customer = Auth::user();

        if ($service->is_deposite == 'yes') {
            // Stripe Payment Integration (only if deposit is required)
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            $session = $stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => ['name' => $service->title],
                        'unit_amount'  => $advance * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.booking.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('stripe.booking.cancel'),
                'metadata'    => [
                    'customer_id'     => $customer->id,
                    'service_id'      => $service->id,
                    'date'            => $request->date,
                    'time_slot_id'    => $request->time_slot_id,
                    'location'        => $request->location ?? '',
                    'payment_method'  => $request->payment_method,
                    'advance'         => $advance,
                ]
            ]);

            return $this->success(['checkout_url' => $session->url], 'Redirect to checkout successfully.', 200);
        }

        // If deposit not required, complete booking immediately
        $request->merge([
            'advance' => 0,
            'customer_id' => $customer->id,
        ]);

        return $this->bookingSuccessfull($request);
    }

    public function bookingSuccessfull(Request $request)
    {
        if ($request->filled('session_id')) {
            // Handle Stripe booking
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            $session = $stripe->checkout->sessions->retrieve($request->session_id);
            $meta_data = $session->metadata;

            $data = [
                'service_id'    => $meta_data->service_id,
                'time_slot_id'  => $meta_data->time_slot_id,
                'date'          => $meta_data->date,
                'location'      => $meta_data->location,
                'payment_method' => $meta_data->payment_method,
                'customer_id'   => $meta_data->customer_id,
                'advance'       => (float) $meta_data->advance,
            ];
        } else {
            // Direct booking (non-deposit service)
            $data = $request->only(['service_id', 'time_slot_id', 'date', 'location', 'payment_method', 'customer_id', 'advance']);
        }

        $service = Service::findOrFail($data['service_id']);
        $data['owner_id'] = $service->owner_id;
        $data['subtotal'] = $service->price;
        $data['invoice_no'] = $this->generateInvoiceNo('Booking');
        $data['tax'] = $service->tax;
        $data['total'] = $service->price + $service->tax;
        $data['due'] = $data['total'] - $data['advance'];
        $data['payment_status'] = $data['advance'] > 0 ? 'paid' : 'unpaid';

        DB::beginTransaction();
        try {
            $data['booking_type'] = 'standard';
            $booking = Booking::create($data);

            $customer = User::find($data['customer_id']); // Required for non-stripe path
            Notification::send($customer, new BookingNotifications(
                'Booking',
                'Booking created successfully',
                $service->title,
                Carbon::now()
            ));

            DB::commit();
            return $this->success($booking, 'Booking created successfully', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error([], 'Booking failed', 500);
        }
    }

    public function bookingCancelled(Request $request)
    {
        return $this->error([], 'Booking Payment cancelled', 200,);
    }

    public function cancelBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $bookingId = $request->booking_id;
        $customer_id = Auth::id();
        $booking = Booking::where('id', $bookingId)->where('customer_id', $customer_id)->first();
        if (!$booking) {
            return $this->error([], 'Booking not found', 404);
        }
        // Check if the booking is already cancelled
        if ($booking->status === 'cancelled') {
            return $this->error([], 'Booking already cancelled', 422);
        }
        $type = 'cancel';
        $reason = $request->reason ?? null;

        if (BookingChangeRequest::where('booking_id', $bookingId)->where('type', $type)->exists()) {
            return $this->error([], 'Booking cancelled request already exists', 422);
        }

        $data = BookingChangeRequest::create([
            'booking_id'  => $bookingId,
            'type'        => $type,
            'reason'      => $reason,
            'customer_id' => $booking->customer_id,
            'owner_id'    => $booking->owner_id,
            'status'      => 'pending',
        ]);

        return $this->success($data, 'Booking cancelled successfully');
    }

    public function rescheduleBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'date' => 'required|date',
            'time_slot_id' => 'required|exists:service_time_slots,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $bookingId = $request->booking_id;
        $customer_id = Auth::id();
        $booking = Booking::where('id', $bookingId)->where('customer_id', $customer_id)->first();
        if (!$booking) {
            return $this->error([], 'Booking not found', 404);
        }
        // Check if the booking is already cancelled
        if ($booking->status === 'cancelled') {
            return $this->error([], 'Booking already cancelled', 422);
        }
        $type = 'reschedule';
        $reason = $request->reason ?? null;

        if (BookingChangeRequest::where('booking_id', $bookingId)->where('type', $type)->exists()) {
            return $this->error([], 'Booking reschedule request already exists', 422);
        }

        $data = BookingChangeRequest::create([
            'booking_id'          => $bookingId,
            'type'                => $type,
            'reason'              => $reason,
            'customer_id'         => $booking->customer_id,
            'owner_id'            => $booking->owner_id,
            'requested_date'      => $request->date,
            'requested_time_slot' => $request->time_slot_id,
            'status'              => 'pending',
        ]);

        return $this->success($data, 'Booking rescheduled successfully');
    }

    public function upcomingBooking()
    {
        $upcomingBookings = Booking::with(['customer', 'timeSlot', 'service', 'owner', 'items'])->where('date', '>=', Carbon::now())->where('customer_id', Auth::id())->get();

        $data = $upcomingBookings->map(function ($item) {
            return [
                'id'            => $item->id,
                'date'          => $item->date,
                'time'          => $item->timeSlot->time,
                'title'         => $item->service->title,
                'location'      => $item->service->location,
                'status'        => $item->status,
                'service_image' => $item->service->image ? asset($item->service->image) : null,
                'owner_name'   => $item->owner->name,
            ];
        });
        return $this->success($data, 'Upcoming Bookings', 200);
    }
    public function bookingHistory()
    {
        $upcomingBookings = Booking::with(['customer', 'timeSlot', 'service', 'owner', 'items'])->where('customer_id', Auth::id())->where('date', '<', Carbon::now())->get();

        $data = $upcomingBookings->map(function ($item) {
            return [
                'date'     => $item->date,
                'time'     => $item->timeSlot->time,
                'title'    => $item->service->title,
                'location' => $item->service->location,
                'status'   => $item->status,
            ];
        });
        return $this->success($data, 'Bookings History', 200);
    }

    //=======================================
    // Customer Methods
    //=======================================
}
