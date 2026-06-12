<?php

namespace App\Api\AdminV1\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     * Set application locale based on lang query parameter
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get language from query parameter, default to 'vi'
        $lang = $request->query('lang', 'vi');

        // Validate language (only allow 'vi' and 'en')
        $allowedLanguages = ['vi', 'en'];
        if (!in_array($lang, $allowedLanguages)) {
            $lang = 'vi';
        }

        // Set application locale
        App::setLocale($lang);

        // Store in request for later use
        $request->merge(['locale' => $lang]);

        return $next($request);
    }
}

