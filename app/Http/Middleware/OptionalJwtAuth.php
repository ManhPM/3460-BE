<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class OptionalJwtAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if ($token) {
            try {
                $user = JWTAuth::setToken($token)->authenticate();
                Auth::login($user);
                $request->setUserResolver(fn() => $user);
            } catch (TokenBlacklistedException $e) {
                // Token is blacklisted, continue without authentication
            } catch (\Exception $e) {
                // Other JWT exceptions, continue without authentication
            }
        }

        return $next($request);
    }
}
