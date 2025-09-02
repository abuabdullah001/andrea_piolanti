<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helper\Helper;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BookmarkArticle;
use App\Models\Interest;
use App\Models\Topic;
use App\Models\UserBehaviour;
use App\Notifications\PostNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use apiresponse;

    public function updateUserInfo(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }

        try {
            $user = Auth::user();

            $user->update($request->only([
                'name',
                'email',
            ]));

            return $this->success($user, 'User updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }

    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password' => 'required|string|max:255',
            'new_password' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }

        try {
            $user = User::where('id', Auth::id())->first();
            if (password_verify($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return $this->success([], "Password changed successfully", 200);
            } else {
                return $this->error([], "Old password is incorrect", 500);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function getMyNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->get();
        return $this->success([
            'notifications' => $notifications,
        ], "Notifications fetched successfully", 200);
    }

    public function deleteUser()
    {
        $user = User::where('id', Auth::id())->first();
        if ($user) {
            $user->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();
            return $this->success([], "User deleted successfully", 200);
        } else {
            return $this->error("User not found", 404);
        }
    }

    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'content' => 'required',
            'url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }
        $user = User::find($request->user_id);
        if ($user) {
            $user->notify(new PostNotification($request->content, $request->url));
            return $this->success([], "Notification sent successfully", 200);
        } else {
            return $this->error("User not found", 404);
        }
    }

    // User Interests
    public function getInterests()
    {
        $interests = Topic::where('status', 'active')->get();
        return $this->success($interests, 'Successfully!', 200);
    }
    public function getUserInterests()
    {
        $user = Auth::user();
        $interests = $user->interests()->withoutGlobalScope('ignoreDefaultTopic')->get(); // Disable global scope for this query

        return $this->success($interests ?? [], 'Successfully!', 200);
    }


    public function storeUserInterests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'interests' => ['required', 'array'],
            'interests.*' => ['required', 'exists:interests,id'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        $user = Auth::user(); // Get authenticated user

        if (!$user) {
            return $this->error([], 'User not found or not authenticated.', 401);
        }

        $user->interests()->sync($request->interests);

        return $this->success([], 'Successfully updated interests!', 200);
    }


    // User Behaviour
    public function getUserBehaviours()
    {
        $user = Auth::id();
        $behaviours = UserBehaviour::where('user_id', $user)->get();
        return $this->success($behaviours, 'Successfully!', 200);
    }
    public function storeUserBehaviour(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'community_post_id' => ['required', 'exists:community_posts,id'],
            'type' => ['required', 'in:like,view,comment,share,bookmark,search'],
            'search_keyword' => ['nullable', 'string', 'max:255'],
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation failed!', 422);
        }

        // Get authenticated user
        $user = Auth::user();

        // If type is "search", ensure search_keyword is present
        if ($request->type === 'search' && !$request->search_keyword) {
            return $this->error([], 'Search keyword is required when type is "search"', 422);
        }

        // Store user behavior
        $userBehaviour = UserBehaviour::create([
            'user_id' => $user->id,
            'community_post_id' => $request->community_post_id,
            'type' => $request->type,
            'search_keyword' => $request->search_keyword,
        ]);

        // Return success response
        return $this->success($userBehaviour, 'User behavior stored successfully!', 201);
    }

    // Store Bookmark Articles
    public function storeBookmarkArticle(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'content' => ['required', 'string'],
            'url' => ['required', 'url', 'max:255'],
            'image' => ['required', 'string', 'max:255'],
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation failed!', 422);
        }

        // Get authenticated user
        $user = Auth::user();

        // Store the bookmark
        $bookmark = BookmarkArticle::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'url' => $request->url,
            'image' => $request->image,
        ]);

        // Return success response
        return $this->success($bookmark, 'Article bookmarked successfully!', 201);
    }

    // Get User Bookmarks
    public function getUserBookmarks()
    {
        $user = Auth::id();
        $bookmarks = BookmarkArticle::where('user_id', $user)->get();
        return $this->success($bookmarks, 'Successfully!', 200);
    }

    // Update Profile Picture
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $user = User::find(Auth::id());

            if ($request->hasFile('profile_picture')) {
                $image = $request->profile_picture;

                if (!empty($user->avatar) && $user->avatar != asset('user.png')) {
                    $relativePath = parse_url($user->avatar, PHP_URL_PATH);
                    $filePath = public_path(ltrim($relativePath, '/'));

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $path = Helper::fileUpload($image, 'profile_pictures/users', $user->username . "-" . time());
                $user->avatar = $path;
                $user->save();

                $imageUrl = asset($user->avatar);

                return $this->success($imageUrl, 'Profile picture updated successfully.', 200);
            }
            return $this->error([], 'No file uploaded.', 422);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
