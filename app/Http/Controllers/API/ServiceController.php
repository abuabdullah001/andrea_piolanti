<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    use apiresponse;

    //=======================================
    // Business Owner Methods
    //=======================================

    public function getOwnerServices(Request $request)
    {
        $services = Service::with([
            'time_slots:id,service_id,time',
            'unavailable_date_and_time_slots:id,service_id,date,time_slot_id'
        ])
            ->where('owner_id', Auth::id());

        if ($request->service_id) {
            $services->where('id', $request->service_id);
        }

        if ($request->title) {
            $services->where('title', 'like', '%' . $request->title . '%');
        }

        // Get the data first
        $services = $services->get();

        // Then map over the collection
        $services = $services->map(function ($service) {
            $service->image = $service->image ? asset($service->image) : asset('default.png');

            // Remove unwanted fields
            unset($service->status, $service->created_at, $service->updated_at, $service->deleted_at);

            return $service;
        });

        if ($services->isEmpty()) {
            return $this->error([], 'No services found', 404);
        }

        return $this->success($services, 'Service retrieved successfully', 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration'    => 'nullable|string',
            'price'       => 'nullable|numeric',
            'service_at'  => 'required|string|in:person,virtual',
            'location'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }
        try {
            $data = $request->all();
            $data['owner_id'] = Auth::id();
            if ($request->has('title')) {
                $data['slug'] = $this->generateUniqueSlug($request->title, Service::class);
            }

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'), null, 'uploads/services', 100, 90, 'service');
            }
            $service = Service::create($data);

            return $this->success([], 'Service created successfully', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function addTimeSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'time'       => 'required|date_format:H:i',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }
        try {
            $service = Service::find($request->service_id);
            $service->time_slots()->create([
                'time' => $request->time,
            ]);
            return $this->success([], 'Time Slot created successfully', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function unavailableTimeSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id'   => 'required|exists:services,id',
            'time_slot_id' => 'required|exists:service_time_slots,id',
            'date'         => 'required|date',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }
        try {
            $service = Service::find($request->service_id);
            $service->unavailable_date_and_time_slots()->create([
                'time_slot_id' => $request->time_slot_id,
                'date'         => $request->date,
                'reason'       => $request->reason ?? null,
            ]);
            return $this->success([], 'Successfully', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    //==========================================================================================================

    //=======================================
    // Customer Methods
    //=======================================

    public function getAllServices(Request $request)
    {
        $services = Service::with([
            'owner:id,category_id,name,avatar',
            'time_slots:id,service_id,time',
            'unavailable_date_and_time_slots:id,service_id,date,time_slot_id'
        ]);

        if ($request->category_id) {
            $services->whereHas('owner', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });
        }

        if ($request->title) {
            $services->where('title', 'like', '%' . $request->title . '%');
        }

        // Get the data first
        $services = $services->get();

        // Then map over the collection
        $services = $services->map(function ($service) {
            $service->image = $service->image ? asset($service->image) : asset('default.png');

            // Remove unwanted fields
            unset(
                $service->status,
                $service->created_at,
                $service->updated_at,
                $service->deleted_at,
                $service->tax,
                $service->minimum_deposite,
                $service->is_deposite,
            );

            return $service;
        });

        if ($services->isEmpty()) {
            return $this->error([], 'No services found', 404);
        }

        return $this->success($services, 'Service retrieved successfully', 200);
    }

    public function details($id)
    {
        $service = Service::with([
            'owner:id,name,avatar',
            'time_slots:id,service_id,time',
            'unavailable_date_and_time_slots:id,service_id,date,time_slot_id'
        ])
            ->select('id', 'title', 'description', 'image', 'price', 'duration', 'owner_id')
            ->where('id', $id)
            ->first();

        if (!$service) {
            return $this->error([], 'Service not found', 404);
        }

        $service->image = $service->image ? asset($service->image) : asset('default.png');

        if ($service->owner && $service->owner->avatar) {
            $service->owner->avatar = asset($service->owner->avatar);
        }

        return $this->success($service, 'Service retrieved successfully', 200);
    }

    // public function availableTimeSlots(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'service_id' => 'required|exists:services,id',
    //         'date'       => 'required|date',
    //     ]);
    //     if ($validator->fails()) {
    //         return $this->error($validator->errors(), 'Validation Error', 422);
    //     }
    //     $service = Service::find($request->service_id);
    //     $unavailable_time_slots = $service->unavailable_date_and_time_slots()->where('date', $request->date)->pluck('time_slot_id')->toArray();
    //     $time_slots = $service->time_slots()->whereNotIn('id', $unavailable_time_slots)->get();
    //     return $this->success($time_slots, 'Time slots retrieved successfully', 200);
    // }

    public function availableTimeSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'date'       => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $service = Service::find($request->service_id);

        // Unavailable slots via custom unavailable relation
        $unavailable_time_slot_ids = $service->unavailable_date_and_time_slots()
            ->where('date', $request->date)
            ->pluck('time_slot_id')
            ->toArray();

        // Booked slots (excluding 'cancel' status)
        $booked_time_slot_ids = $service->bookings()
            ->where('date', $request->date)
            ->where('status', '!=', 'cancel') // Only active bookings
            ->pluck('time_slot_id')
            ->toArray();

        // Combine both arrays and make unique
        $disabled_slot_ids = array_unique(array_merge($unavailable_time_slot_ids, $booked_time_slot_ids));

        // Get all slots and map with disable flag
        $all_time_slots = $service->time_slots()->get();

        $time_slots = $all_time_slots->map(function ($slot) use ($disabled_slot_ids) {
            return [
                'id' => $slot->id,
                'time' => $slot->time,
                'disable' => in_array($slot->id, $disabled_slot_ids),
                'message' => in_array($slot->id, $disabled_slot_ids) ? 'Not available' : null,
            ];
        });

        return $this->success($time_slots, 'Time slots retrieved successfully', 200);
    }
}
