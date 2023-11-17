<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
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
        try{
            // Pre-Middleware Action
            JWTAuth::parseToken()->authenticate();
            $response = $next($request);        
            
        } catch (Exception $e){
            if($e instanceof TokenInvalidException){
                return response()->json(['status' => 'invalid token'], 401);
            }
            if($e instanceof TokenExpiredException){
                return response()->json(['status' => 'expired token'], 401);
            }
            
            return response()->json(['status' => 'token not found'], 401);
        } finally {
            return $response;
        }

    }
}
