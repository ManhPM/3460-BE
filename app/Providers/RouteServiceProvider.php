<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->as('api.v1.')
                ->prefix('/api/v1')
                ->group(base_path('routes/api/v1.php'));

            // Admin API v1 Routes
            Route::middleware('api')
                ->as('api.admin-v1.')
                ->prefix('/api/admin-v1')
                ->group(base_path('routes/api/admin-v1.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->prefix('/admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));

            if (env('IS_COMBO')) {
                Route::middleware('web')
                    ->namespace($this->namespace)
                    ->prefix('/')
                    ->as('user.')
                    ->group(base_path('routes/web.php'));
            }

            // Redirect root path to admin login
            Route::middleware('web')
                ->get('/', function () {
                    return redirect()->route('admin.login.index');
                });
        });

        // Fallback route: redirect 404 to admin login (must be registered after all routes)
        Route::fallback(function () {
            return redirect()->route('admin.login.index');
        })->middleware('web');
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
