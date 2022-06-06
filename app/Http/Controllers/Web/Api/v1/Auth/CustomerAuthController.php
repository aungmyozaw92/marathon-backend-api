<?php

namespace App\Http\Controllers\Web\Api\v1\Auth;

use DB;
use Validator;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Claims\Custom;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Zone\ZoneResource;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class CustomerAuthController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {

        $this->guard = \Auth::guard('api');
    }

    public function phone_login(Request $request)
    {
        Config::set('auth.defaults.guard', 'customer');
        $credentials = $request->only('phone');

        $rules = ['phone' => 'required',];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 2,
                'message' => $validator->messages(),
            ], 200);
        }

        $customer = Customer::where('phone', $request->input('phone'))->first();
        try { 
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($customer)) { 
                return response()->json(['error' => 'invalid_credentials'], 401);
            } 
        } catch (JWTException $e) { 
            // something went wrong 
            return response()->json(['error' => 'could_not_create_token'], 500); 
        } 

        if($token){
           $customer->token = $token;
           $customer->save();
        }
        // if no errors are encountered we can return a JWT 

        //  return response()->json(compact('token')); 
        return $this->respondWithToken($token,$customer);
    }

    public function logout(Request $request)
    {
        // Get JWT Token from the request header key "Authorization"
        $token = $request->header('Authorization');
        // Invalidate the token
        try {
            $header_token = explode('Bearer ', $token);
            Customer::where('token', $header_token[1])->update(['token' => null]);
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

    public function profile()
    {
        $user = auth()->user();
        return new CustomerResource($user->load(['city','zone']));
    }
    protected function respondWithToken($token,$user)
    {
        return response()->json([
            'status' => 1,
            'data' => [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'other_phone' => $user->other_phone,
                    'email' => $user->email,
                    'address' => $user->address,
                    'city_id' => $user->city_id,
                    'zone_id' => $user->zone_id,
                    'city' => CityResource::make($user->city),
                    'zone' => ZoneResource::make($user->zone),
                ],
                'token_type' => 'bearer',
                ],
        ], 200);
    }

    public function guard()
    {
        return \Auth::Guard('api');
    }
}
