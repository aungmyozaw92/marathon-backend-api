<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    public function handle($request, Closure $next)
    {
        
        JWTAuth::parseToken()->authenticate();
        if (auth()->guard('api')->check()) {
            \Config::set('auth.defaults.guard', 'api');
            $user_token = auth('api')->user()->token;
        } elseif (auth()->guard('merchant')->check()) {
            \Config::set('auth.defaults.guard', 'merchant');
            $user_token = auth('merchant')->user()->token;
        } elseif (auth()->guard('delivery')->check()) {
            \Config::set('auth.defaults.guard', 'delivery');
            $user_token = auth('delivery')->user()->token;
        } elseif (auth()->guard('operation')->check()) {
            \Config::set('auth.defaults.guard', 'operation');
            $user_token = auth('operation')->user()->token;
        } elseif (auth()->guard('agent')->check()) {
            \Config::set('auth.defaults.guard', 'agent');
            $user_token = auth('agent')->user()->token;
        }elseif (auth()->guard('customer')->check()) {
            \Config::set('auth.defaults.guard', 'customer');
            $user_token = auth('customer')->user()->token;
        }
        
        $token = $request->header('Authorization');
        $header_token = explode('Bearer ', $token);
        // dd($user_token != $header_token[1]);
        if ($user_token != $header_token[1]) {
            $mobile_operation = str_contains(request()->url(),'mobile/api/v1/operation/');
            // if ( (auth()->guard('api')->check() && auth()->guard('api')->user()->department_id != 3) || auth()->guard('delivery')->check() ) {
            if (auth()->guard('api')->check() && auth()->guard('customer')->check()) {
                    JWTAuth::invalidate($token);
                    return response(['status' => 3, 'message' => 'Token can not be used, get new one'], Response::HTTP_OK);
            }
            if (auth()->guard('customer')->check()) {
                JWTAuth::invalidate($token);
                return response(['status' => 3, 'message' => 'Token can not be used, get new one'], Response::HTTP_OK);
        }
        }

        return $next($request);
    }
}
