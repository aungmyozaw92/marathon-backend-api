<?php

namespace App\Http\Middleware;

use Closure;

class CheckCalculatorToken
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
        $header = $request->header('Authorization');
        if ($header == "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9") {
            return $next($request);
        }

        return response()->json(["status" => 2, "message" => "Unauthenticated!"]);
    }
}
