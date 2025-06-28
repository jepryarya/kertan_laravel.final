<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // ... (Biarkan seperti yang sudah ada)
        ],

        'api' => [
            // Tempatkan middleware CORS kustom Anda di awal grup 'api'
            \App\Http\Middleware\Cors::class, // <-- Pastikan ini di sini dan di awal!
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Aktifkan jika Anda pakai Laravel Sanctum untuk SPA/Mobile
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or individual routes.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // ... (Biarkan seperti yang sudah ada)
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // === Middleware Kustom Anda ===
        'cekrole' => \App\Http\Middleware\CekRole::class,
        'admin2' => \App\Http\Middleware\CekRole::class,
        'ionic.role' => \App\Http\Middleware\IonicRole::class,
    ];
}