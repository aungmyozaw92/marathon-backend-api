<?php

namespace App\Http\Controllers\Web\Api\v1\Customer\Auth;
use DB;
use Validator;
use App\Models\User;
use App\Models\Customer;
use App\Services\SmsService;
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

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function requestCode(Request $request)
    {
        $credentials = $request->only('phone');

        $rules = ['phone' => 'required',];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 2,
                'message' => $validator->messages(),
            ], 200);
        }

       $customer = Customer::where('phone', $request->get('phone'))->first();
       if($customer){
          $sms_request_id = $this->smsService->verifyRequest($request->get('phone'));
          $customer['sms_request_id'] = $sms_request_id;
          return $customer;
       }else{
            return response()->json([
                'status' => 2,
                'message' => "Does not exit",
            ], 200); 
       }
    }

    public function verifyCode(Request $request)
    {
        $credentials = $request->only('phone','sms_request_id','code');

        $rules = ['phone' => 'required',
                  'sms_request_id' => 'required',
                  'code' => 'required',];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 2,
                'message' => $validator->messages(),
            ], 200);
        }

        $customer = Customer::where('phone', $request->get('phone'))->firstOrFail();

        $data = $this->smsService->verify($request->get('sms_request_id'),$request->get('code'));

        if($data){
            $token = $this->login($customer);
            return $this->respondWithToken($token,$customer);
        }else{
            return response()->json([
                'status' => 2,
                'message' => 'Verification Fail.',
            ], 200);
        }
    }

    public function login($customer)
    {
        try { 
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($customer)) { 
                return response()->json(['error' => 'invalid_credentials'], 401);
            } 
        } catch (JWTException $e) { 
            // something went wrong 
            return response()->json(['error' => 'Something wrong'], 500); 
        } 

        if($token){
           $customer->token = $token;
           $customer->save();
        }

        return $token;
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
