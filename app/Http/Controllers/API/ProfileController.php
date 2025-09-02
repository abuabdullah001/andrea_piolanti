<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserImage;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use apiresponse;

    // Index
    public function index()
    {
        $authUser = Auth::user();

        // Fetch user with selected fields and eager-load latest services
        $user = User::with(['services' => function ($query) {
            $query->select('id', 'owner_id', 'title', 'slug', 'price', 'image', 'duration')->latest();
        }])->select('id', 'name', 'email', 'avatar', 'about_me', 'description')->find($authUser->id);

        // Convert avatar path
        $user->avatar = asset($user->avatar);

        // Convert service image paths
        foreach ($user->services as $service) {
            $service->image = asset($service->image);
        }

        return $this->success($user, 'User profile retrieved successfully', 200);
    }

    // Update
    public function update(Request $request)
    {
        $user = Auth::user();
        if ($request->has('avatar')) {
            $oldImage = $user->avatar == 'user.png' ? null : $user->avatar;
            $data['avatar'] = $this->uploadImage($request->file('avatar'), $oldImage, 'uploads/users/avatars/', 93, 74, 'user');
        } else {
            $data['avatar'] = $user->avatar;
        }
        $data['name']           = $request->name;
        $data['about_me']       = $request->about_me;
        $data['description']    = $request->description;
        $data['social_profile'] = $request->social_profile ?? null;

        if ($request->has('images')) {
            $lastSerial = UserImage::where('user_id', $user->id)->max('serial') ?? 0;
            $serial = $lastSerial + 1;
            foreach ($request->images as $image) {
                $uploadedImage = $this->uploadImage($image, null, 'uploads/users/covers/', 330, 150, 'user');
                UserImage::create([
                    'user_id' => $user->id,
                    'image'   => $uploadedImage,
                    'serial'  => $serial++,
                ]);
            }
        }

        $user->update($data);
        return $this->success($user, 'User profile updated successfully', 200);
    }

    // Delete Image
    public function coverImageRemove(Request $request)
    {
        $user = Auth::user();
        $image = UserImage::where('id', $request->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$image) {
            return $this->error([], 'Image not found', 404);
        }

        // Delete the file
        if ($image->image && file_exists(public_path($image->image))) {
            unlink(public_path($image->image));
        }

        // Store serial before deleting
        $deletedSerial = $image->serial;
        $image->delete();

        // Reorder all images with serial > deletedSerial
        $imagesToUpdate = UserImage::where('user_id', $user->id)
            ->where('serial', '>', $deletedSerial)
            ->orderBy('serial')
            ->get();

        foreach ($imagesToUpdate as $img) {
            $img->serial = $img->serial - 1;
            $img->save();
        }

        return $this->success([], 'Image deleted and serials updated successfully', 200);
    }

    public function updateContactInfo(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'email' => 'required|email',
            'preffered_contact' => 'required|in:email,phone',
            'communication' => 'required',
        ]);
        $user = Auth::user();
        $user->update([
            'phone'             => $request->phone,
            'email'             => $request->email,
            'preffered_contact' => $request->preffered_contact,
            'communication'     => $request->communication == 1 ? true : false,
        ]);
        return $this->success($user, 'Contact info updated successfully', 200);
    }

    public function updateUserImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image', 'max:2048'], // Optional: max 2MB limit
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $user = auth()->user();

            $oldImage = $user->avatar !== 'user.png' ? $user->avatar : null;

            // Upload the new image (assuming your helper handles deleting old one)
            $uploadedImage = $this->uploadImage(
                $request->file('image'),
                $oldImage,
                'uploads/users/',
                95,
                95,
                'avatar'
            );

            $user->update([
                'avatar' => $uploadedImage,
            ]);

            DB::commit();

            return $this->success([
                'avatar' => asset('uploads/users/' . $uploadedImage)
            ], 'Image uploaded successfully', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error([], 'Image upload failed: ' . $e->getMessage(), 400);
        }
    }
}
