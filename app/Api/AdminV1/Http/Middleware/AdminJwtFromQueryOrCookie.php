<?php

namespace App\Api\AdminV1\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AdminJwtFromQueryOrCookie
{
    /**
     * Handle an incoming request.
     * Lấy token từ query parameter hoặc cookie thay vì header
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ưu tiên lấy từ query parameter
        $token = $request->query('access_token');

        // Nếu không có trong query, lấy từ cookie
        if (!$token) {
            $token = $request->cookie('admin_access_token');
        }

        if ($token) {
            // Set token vào header Authorization để JWT middleware có thể parse
            $request->headers->set('Authorization', 'Bearer ' . $token);

            // Set token cho JWT để đảm bảo nó được sử dụng
            try {
                JWTAuth::setToken($token);
            } catch (\Exception $e) {
                // Nếu token không hợp lệ, tiếp tục xử lý request
                // Middleware auth:admin-api sẽ xử lý việc reject request
            }
        }

        return $next($request);
    }
}

