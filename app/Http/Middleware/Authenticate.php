<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Cho API routes, luôn trả về JSON
        if ($request->is('api/*')) {
            return null; // Sẽ được xử lý bởi Handler::unauthenticated
        }

        // Cho web routes, redirect về trang chủ
        if (! $request->expectsJson()) {
            return '/';
        }

        return null;
    }
}
