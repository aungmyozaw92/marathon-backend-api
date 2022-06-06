<?php
namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $staff = auth('api')->user();
       
        if ($request->user() === null) {
            return response(['status' => 2, 'message' => 'User have not permission for this page access.'], Response::HTTP_OK);
        }
        $actions = $request->route()->getAction();
        $roles = isset($actions['roles']) ? $actions['roles'] : null;

        if ($staff->hasAnyRole($roles) || !$roles) {
            return $next($request);
        }
        return response(['status' => 2, 'message' => 'User have not permission for this page access.'], Response::HTTP_OK);
    }
}
