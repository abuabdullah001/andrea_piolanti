<?php

namespace App\Http\Controllers\API;

use App\Traits\apiresponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    use apiresponse;
    // Add
    public function add(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $serviceId = $request->input('service_id');
            // check if the service already exists in the user's favourites
            $existingFavourite = $user->favourites()->where('service_id', $serviceId)->first();
            if ($existingFavourite) {
                return $this->error([], 'Service already exists in favourites.', 400);
            }
            $user->favourites()->attach($serviceId);
            $favourite = $user->favourites()->get();
            return $this->success($favourite, 'Service added to favourites successfully.', 200);
        } else {
            return $this->error([], 'User not authenticated.', 401);
        }
    }
    // Get
    public function get()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $favourites = $user->favourites()->get();

            // Clean up the output
            $favourites = $favourites->map(function ($service) {
                $service->image = $service->image ? asset($service->image) : asset('default.png');

                // Remove unwanted fields
                unset(
                    $service->is_deposite,
                    $service->minimum_deposite,
                    $service->tax,
                    $service->pivot,
                    $service->deleted_at,
                    $service->updated_at,
                    $service->created_at,
                    $service->status,
                    $service->duration,
                    $service->description
                );

                return $service;
            });

            return $this->success($favourites, 'Successfully.', 200);
        } else {
            return $this->error([], 'User not authenticated.', 401);
        }
    }


    // Remove
    public function remove($id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $serviceId = $id;
            $user->favourites()->detach($serviceId);
            $favourite = $user->favourites()->get();
            return $this->success($favourite, 'Service removed from favourites successfully.', 200);
        } else {
            return $this->error([], 'User not authenticated.', 401);
        }
    }
}
