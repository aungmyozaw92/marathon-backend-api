<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery\Auth;

use DB;
use JWTAuth;
use Validator;
use App\Models\Delivery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\Mobile\Delivery\Staff\StaffResource;
use App\Http\Requests\Mobile\Delivery\Profile\UpdateProfileRequest;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        Config::set('auth.defaults.guard', 'delivery');
        $credentials = $request->only('username', 'password');

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 2,
                'message' => $validator->messages(),
            ], 200);
        }
        $staff = null;
        if (auth()->validate($credentials)) {
            $staff = Delivery::where('username', $request->input('username'))->first();
        }
        if (!$staff) {
            return response()->json(['status' => 2, 'message' => 'Wrong credentials.'], 200);
        }

        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json(['status' => 2, 'message' => 'Wrong credentials.'], 200);
        }

        Delivery::where('id', $staff->id)->update(['token' => $token]);

        return $this->respondWithToken($token);
    }

    /**
     * Profile
     */
    public function profile()
    {
        $staff = $this->guard()->user();

        return new StaffResource($staff->load(['department', 'zone', 'courier_type', 'hero_badge']));
    }

    public function update_profile(UpdateProfileRequest $request)
    {
        $staff = $this->guard()->user();

        $staff = $request->updateProfile($staff);

        return response()->json(['status' => 1, 'message' => 'Successfully updated!'], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // Get JWT Token from the request header key "Authorization"
        $token = $request->header('Authorization');

        // Invalidate the token
        try {
            JWTAuth::invalidate($token);

            return response()->json([
                'status' => 1,
                'message' => 'User successfully logged out.',
            ], 200);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
              'status' => 2,
              'message' => 'Failed to logout, please try again.',
            ], 200);
        }
    }

    /**
     * Login Respond With Token
     */
    protected function respondWithToken($token)
    {
        $user = $this->guard()->user();
        $staff =  new StaffResource($user->load(['department', 'zone', 'courier_type']));
        return response()->json([
            'status' => 1,
            'data' => [
                'access_token' => $token,
                'user' => $staff,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ], 200);
    }

    /**
     * Authorization Guard Merchant
     */
    public function guard()
    {
        return \Auth::Guard('delivery');
    }
}
