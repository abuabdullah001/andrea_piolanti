<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use apiresponse;

    //=======================================
    // Business Owner Methods
    //=======================================

    public function getOwnerReviews(Request $request)
    {
        $ownerId = Auth::id();

        // Fetch reviews given directly to the Owner (User model)
        $ownerReviews = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $ownerId)
            ->with('user') // the customer who gave the review
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'customer_id' => $review->customer_id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'type' => $review->rating < 3 ? 'critical' : 'positive',
                    'user' => [
                        'name' => $review->user->name,
                        'email' => $review->user->email,
                        'avatar' => asset($review->user->avatar),
                        'phone' => $review->user->phone,
                        'address' => $review->user->address,
                    ],
                ];
            });

        $ownerAverageRating = $ownerReviews->avg('rating') ?? 0;

        // Fetch Service IDs owned by this owner
        $ownedServiceIds = Service::where('owner_id', $ownerId)->pluck('id');

        // Fetch reviews given to Services
        $serviceReviews = Review::where('reviewable_type', Service::class)
            ->whereIn('reviewable_id', $ownedServiceIds)
            ->with('user')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'customer_id' => $review->customer_id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'type' => $review->rating < 3 ? 'critical' : 'positive',
                    'user' => [
                        'name' => $review->user->name,
                        'email' => $review->user->email,
                        'avatar' => asset($review->user->avatar),
                        'phone' => $review->user->phone,
                        'address' => $review->user->address,
                    ],
                ];
            });

        $serviceAverageRating = $serviceReviews->avg('rating') ?? 0;

        return $this->success([
            'owner_reviews' => $ownerReviews,
            'owner_average_rating' => round($ownerAverageRating, 2),
            'service_reviews' => $serviceReviews,
            'service_average_rating' => round($serviceAverageRating, 2),
        ], 'Fetched owner-related reviews successfully', 200);
    }

    //=======================================
    // Business Owner Methods
    //=======================================


    //==========================================================================================================

    //=======================================
    // Customer Methods
    //=======================================

    public function getCustomerReviews(Request $request)
    {
        $reviewable_type = match ($request->reviewable_type) {
            'service'       => Service::class,
            'businessOwner' => User::class,
        };

        $reviews = Review::where('reviewable_id', $request->reviewable_id)
            ->where('reviewable_type', $reviewable_type)
            ->where('status', $request->status)
            ->with('reviewable', 'user')
            ->get();

        $data = $reviews->map(function ($review) {
            return [
                'id'      => $review->id,
                'rating'  => $review->rating,
                'comment' => $review->comment,

                'reviewable' => $review->reviewable ? [
                    'id'       => $review->reviewable->id,
                    'title'    => $review->reviewable->title ?? null,
                    'location' => $review->reviewable->location ?? null,
                    'image'    => $review->reviewable->image ? asset($review->reviewable->image) : asset('default.png'),
                ] : null,

                'user' => $review->user ? [
                    'id'       => $review->user->id,
                    'name'     => $review->user->name,
                    'username' => $review->user->username,
                    'avatar'   => $review->user->avatar ? asset($review->user->avatar) : asset('default-user.png'),
                ] : null,
            ];
        });

        return $this->success($data, 'Fetched reviews successfully', 201);
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'reviewable_id'   => 'required|integer',
                'reviewable_type' => 'required|string|in:service,businessOwner',
                'rating'          => 'required|integer|min:1|max:5',
                'comment'         => 'nullable|string',
            ]);

            $reviewable_type = match ($request->reviewable_type) {
                'service'       => Service::class,
                'businessOwner' => User::class,
            };

            $review = Review::create([
                'customer_id'     => Auth::id(),
                'reviewable_id'   => $request->reviewable_id,
                'reviewable_type' => $reviewable_type,
                'rating'          => $request->rating,
                'comment'         => $request->comment,
            ]);

            return $this->success([], 'Review added successfully', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }

    public function likeDislikeReview(Request $request)
    {
        try {
            $request->validate([
                'review_id' => 'required|integer',
            ]);

            $review = Review::find($request->review_id);

            if (!$review) {
                return $this->error([], 'Review not found', 404);
            }

            if ($review->likes()->where('user_id', Auth::id())->exists()) {
                $review->likes()->detach(Auth::id());
                return $this->success([], 'Review unliked successfully', 200);
            } else {
                $review->likes()->attach(Auth::id());
                return $this->success([], 'Review liked successfully', 200);
            }


        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }

    //=======================================
    // Customer Methods
    //=======================================

}
