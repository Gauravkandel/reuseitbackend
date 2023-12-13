<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserOtp;
use App\Notifications\WelcomeMessageNotification;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            // Check if the email exists in the database
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                // Email not found in the database
                return response()->json(['error' => 'Email not found'], 401);
            }

            // Check if the password is incorrect
            if (!Hash::check($credentials['password'], $user->password)) {
                // Password doesn't match
                return response()->json(['error' => 'Password incorrect'], 401);
            }

            // Default unauthorized response if neither condition is met
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = User::where('email', $credentials['email'])->first();
        $user->ActiveStatus = true;
        $user->save();
        return $this->respondWithToken($token);
    }
    public function register(UserRequest $request)
    {
        $userValidation = $request->validated();
        if ($request->has('Profile_image')) {
            $profile = $request->file('Profile_image');
            $profile_name = time() . $profile->getClientOriginalName();
            $profile->move(public_path('images'), $profile_name);
            $userValidation['Profile_image'] = $profile_name;
        }
        $userdata = User::create($userValidation);
        Notification::send($userdata, new WelcomeMessageNotification);
        return response()->json(['message' => "successfully registered"], 200);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        $user->products;
        $user->otpdata = UserOtp::where('user_id', auth()->id())->latest()->first();
        foreach ($user->products as $product) {
            $product->image; // Access images relationship for each product
            // Now $images contains the images associated with the current product
        }

        return response()->json(auth()->user());
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $now = now();
        $userdata = User::find(auth()->id());
        $userdata->ActiveStatus = false;
        $userdata->ActiveTime = $now;
        $userdata->save();
        auth()->logout();
        $cookie = Cookie::forget('jwt');
        return response()->json(['message' => 'Successfully logged out'])->withCookie($cookie);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();
        $notifications = $user->unreadNotifications->count();
        $user_details = [
            'user' => auth()->user(),
        ];
        $cookie = cookie('jwt', $token, auth()->factory()->getTTL(60 * 24 * 24 * 60 * 365), null, null, false, true);
        return response()->json($user_details)->withCookie($cookie);
    }
}

// public function generateToken() {
//     $token = auth()->user()->createToken('auth-token')->plainTextToken;

//     return response()
//         ->json(['token' => $token])
//         ->cookie('auth-token', $token, 60); // 60 minutes, adjust as needed
// }