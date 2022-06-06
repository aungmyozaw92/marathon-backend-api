<?php

namespace App\Http\Controllers\Web\Api\v1\SuperMerchant\Auth;

use DB;
use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        Config::set('auth.defaults.guard', 'merchant');
        Config::set('jwt.ttl', 43200);

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
        if (auth()->validate($credentials)) {
            $merchant = DB::table('merchants')->where('username', $request->input('username'))->first();
        }

        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json(['status' => 2, 'message' => 'Wrong credentials.'], 200);
        }

        DB::table('merchants')->where('id', $merchant->id)->update(['token' => $token]);

        return $this->respondWithToken($token);
    }

    /**
     * For Refresh Token
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Login Respond With Token
     */
    protected function respondWithToken($token)
    {
        $user = $this->guard()->user();
        return response()->json([
            'status' => 1,
            'data' => [
                'access_token' => $token,
                // 'user' => [
                //     //$merchant,
                //     // 'id' => $user->id,
                //     // 'name' => getConvertedString($user->name),
                //     // 'username' => $user->username,
                //     // 'city_id' => $user->city_id,
                //     // 'branches' => $merchant,
                //     // 'staff' => $user->staff
                // ],
                'token_type' => 'bearer',
                // 'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ], 200);
    }
    /**
     * Authorization Guard Merchant
     */
    public function guard()
    {
        return \Auth::Guard('merchant');
    }
}
