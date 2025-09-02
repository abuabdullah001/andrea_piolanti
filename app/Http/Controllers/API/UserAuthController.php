<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    use apiresponse;

    public function customerRegister(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'unique:users'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:1', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $data = $request->all();

            $data['username'] = $this->checkUserName($request->name);

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            $user->assignRole('user');

            // $this->generateOtp($user);

            DB::commit();

            $userData = [
                'id' => $user->id,
                'avatar' => asset('uploads/avatar/' . $user->avatar),
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'about_me' => $user->about_me,
                'description' => $user->description,
            ];

            return $this->success([
                'role'  => $user->getRoleNames()->first(),
                'user'  => $userData,
                'token' => $this->respondWithToken(JWTAuth::fromUser($user)),
            ], 'Registered Successfully OTP Sent to your email Please Verify', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 400);
        }
    }
    public function ownerRegister(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'unique:users'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:1', 'confirmed'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $data = $request->all();

            $data['username'] = $this->checkUserName($request->name);

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            $user->assignRole('owner');

            // $this->generateOtp($user);

            DB::commit();

            $category = $user->category;

            $userData = [
                'id' => $user->id,
                'avatar' => asset('uploads/avatar/' . $user->avatar),
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'about_me' => $user->about_me,
                'description' => $user->description,
            ];

            if ($category) {
                $userData['category_id'] = $category->id;
                $userData['category_name'] = $category->name;
            }

            return $this->success([
                'role'  => $user->getRoleNames()->first(),
                'user'  => $userData,
                'token' => $this->respondWithToken(JWTAuth::fromUser($user)),
            ], 'Registered Successfully OTP Sent to your email Please Verify', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 400);
        }
    }
    // public function register(Request $request)
    // {
    //     // Validate the request data
    //     $validator = Validator::make($request->all(), [
    //         'name'        => ['required', 'string', 'max:255'],
    //         'phone'       => ['required', 'string', 'unique:users'],
    //         'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password'    => ['required', 'string', 'min:8', 'confirmed'],
    //         'role'        => ['required', 'string', 'in:admin,user,owner'],
    //         'category_id' => ['required', 'exists:categories,id'],
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->error([], $validator->errors(), 422);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $data = $request->all();

    //         $data['password'] = Hash::make($data['password']);

    //         $data['avatar'] = 'user.png';
    //         if ($request->hasFile('avatar')) {
    //             $data['avatar'] = $this->fileUpload(
    //                 $request->file('avatar'),
    //                 'uploads/users/avatars/'
    //             );
    //         }

    //         if ($request->role == 'owner') {
    //             $data['category_id'] = $request->category_id;
    //         } else {
    //             $data['category_id'] = null;
    //         }

    //         $user = User::create($data);

    //         $user->assignRole($request->role);

    //         // $this->generateOtp($user);

    //         DB::commit();

    //         return $this->success([
    //             'user'  => $user->only('id', 'email', 'phone', 'role'),
    //             'token' => $this->respondWithToken(JWTAuth::fromUser($user)),
    //         ], 'Check your email to verify your account', 200);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return $this->error([], $e->getMessage(), 400);
    //     }
    // }

    public function login(Request $request)
    {
        if ($request->has(['email', 'password'])) {
            return $this->loginWithEmail($request);
        }

        if ($request->has(['phone', 'otp'])) {
            return $this->loginWithPhone($request);
        }

        return $this->error([], 'Invalid login request. Please provide email/password or phone/otp.', 400);
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $this->generateOtp($user);

        return $this->success([], 'Check Your Email for Password Reset Otp', 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'otp'      => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user->otp || !Hash::check($request->otp, $user->otp)) {
            return response()->json([
                'message' => 'Invalid OTP!',
            ], 400);
        }

        if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
            return response()->json([
                'message' => 'OTP has expired.',
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_created_at = null;
        $user->save();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }

    public function varifyOtpWithOutAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => 'required|email|exists:users,email',
            'otp'    => 'required|numeric',
            'action' => 'required|in:email_verification,forgot_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($request->action == 'email_verification') {
            if ($user->email_verified_at) {
                return response()->json([
                    'message' => 'Email already verified.',
                ], 400);
            }
            if (!Hash::check($request->otp, $user->otp)) {
                return response()->json([
                    'message' => 'Invalid OTP!',
                ], 400);
            }
            if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
                return response()->json([
                    'message' => 'OTP has expired.',
                ], 400);
            }
            $user->email_verified_at = now();
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();

            $category = $user->category;

            $userData = [
                'id' => $user->id,
                'avatar' => asset('uploads/avatar/' . $user->avatar),
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'about_me' => $user->about_me,
                'description' => $user->description,
            ];

            if ($category) {
                $userData['category_id'] = $category->id;
                $userData['category_name'] = $category->name;
            }


            return $this->success([
                'role'  => $user->getRoleNames()->first(),
                'user'  => $userData,
                'token' => $this->respondWithToken(JWTAuth::fromUser($user)),
            ], 'Verification Successful', 200);
        }

        if ($request->action == 'forgot_password') {
            if (!$user->otp || !Hash::check($request->otp, $user->otp)) {
                return response()->json([
                    'message' => 'Invalid OTP!',
                ], 400);
            }

            if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
                return response()->json([
                    'message' => 'OTP has expired.',
                ], 400);
            }

            return response()->json([
                'message' => 'OTP verified successfully.',
            ], 200);
        }
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error([], 'User not found', 404);
        }
        $this->generateOtp($user);
        return $this->success([], 'Check Your Email for Password Reset Otp', 200);
    }


    protected function loginWithEmail(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->error([], 'Invalid credentials', 401);
        }

        $user = Auth::user();

        // if (is_null($user->email_verified_at)) {
        //     $this->generateOtp($user);
        //     return $this->error([], 'Check your email to verify your account', 401);
        // }

        // ==========================================
        // Transform User Data
        // ==========================================

        $category = $user->category;
        $role = $user->getRoleNames()->first(); // Spatie: gets the first assigned role

        $userData = [
            'id' => $user->id,
            'avatar' => asset('uploads/avatar/' . $user->avatar),
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'about_me' => $user->about_me,
            'description' => $user->description,
        ];

        if ($category) {
            $userData['category_id'] = $category->id;
            $userData['category_name'] = $category->name;
        }

        return $this->success([
            'role' => $role,
            'token' => $this->respondWithToken($token),
            'user' => $userData,
        ], 'User logged in successfully.', 200);
    }


    protected function loginWithPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
            'otp'   => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        if ($user->otp !== (int) $request->otp) {
            return $this->error([], 'Invalid OTP', 401);
        }

        $user->otp = null;
        $user->save();

        if (is_null($user->email_verified_at)) {
            $this->generateOtp($user);
            return $this->error([], 'Check your email to verify your account', 401);
        }

        $token = JWTAuth::fromUser($user);

        // ==========================================
        // Transform User Data
        // ==========================================

        $category = $user->category;
        $role = $user->getRoleNames()->first(); // Spatie: gets the first assigned role

        $userData = [
            'id' => $user->id,
            'avatar' => asset('uploads/avatar/' . $user->avatar),
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'about_me' => $user->about_me,
            'description' => $user->description,
        ];

        if ($category) {
            $userData['category_id'] = $category->id;
            $userData['category_name'] = $category->name;
        }

        return $this->success([
            'role' => $role,
            'token' => $this->respondWithToken($token),
            'user' => $userData,
        ], 'User logged in successfully.', 200);
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null,
        ]);
    }

    // Social Login / OAuth Login
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'role' => 'required|in:user,owner', // User or Owner
        ]);

        $client = new \Google_Client(['client_id' => config('services.google.client_id')]);

        $payload = $client->verifyIdToken($request->id_token);

        if (!$payload) {
            return $this->error([], 'Invalid Google ID token.', 401);
        }

        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'],
                'username' => $this->checkUserName($payload['name']),
                'email_verified_at' => now(),
                'avatar' => $payload['picture'] ?? null,
                'password' => Hash::make(Str::random(16)), // Random password
            ]
        );

        $user->assignRole($request->role); // You can customize role logic

        $token = JWTAuth::fromUser($user);

        return $this->success([
            'role'  => $user->getRoleNames()->first(),
            'user'  => [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'about_me' => $user->about_me,
                'description' => $user->description,
            ],
            'token' => $this->respondWithToken($token),
        ], 'Login with Google successful.');
    }

    public function appleLogin(Request $request)
    {
        $request->validate([
            'identity_token' => 'required|string',
            'role' => 'required|in:user,owner',

        ]);

        $appleSignInPayload = (new \Lcobucci\JWT\Parser())->parse($request->identity_token);
        $email = $appleSignInPayload->getClaim('email');
        $name = $request->input('name', 'Apple User');

        if (!$email) {
            return $this->error([], 'Invalid Apple identity token.', 401);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'username' => $this->checkUserName($name),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(16)),
            ]
        );

        $user->assignRole($request->role);

        $token = JWTAuth::fromUser($user);

        return $this->success([
            'role'  => $user->getRoleNames()->first(),
            'user'  => [
                'id' => $user->id,
                'avatar' => $user->avatar,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'about_me' => $user->about_me,
                'description' => $user->description,
            ],
            'token' => $this->respondWithToken($token),
        ], 'Login with Apple successful.');
    }
}
