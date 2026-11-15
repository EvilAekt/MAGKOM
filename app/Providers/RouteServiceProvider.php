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
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // SEC-003: Multi-Layer Login Rate Limiting (Account, IP, Pair, Global)
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email')));
            $accountKey = 'login_route_act:'.sha1($email);
            $pairKey = 'login_route_pair:'.sha1($email.'|'.$request->ip());

            return [
                // Layer 3: Kombinasi Akun + IP (Maks 3 per menit)
                Limit::perMinute(3)->by($pairKey),
                // Layer 1: Per-Akun terlepas dari IP (Maks 5 per menit untuk tangkal distributed botnet)
                Limit::perMinute(5)->by($accountKey),
                // Layer 2: Per-IP (Maks 15 per menit untuk cegah credential stuffing)
                Limit::perMinute(20)->by($request->ip()),
                // Layer 4: Global spike limiter
                Limit::perMinute(300),
            ];
        });

        // NFR-S2: Proteksi spam pendaftaran akun & formulir (10 per jam)
        RateLimiter::for('pendaftaran', function (Request $request) {
            return Limit::perHour(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
