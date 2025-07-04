<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Tambahkan use untuk middleware yang kamu buat
use App\Http\Middleware\CekRole;
use App\Http\Middleware\IonicRole;
use App\Http\Middleware\Cors; // Pastikan ini ada

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Pastikan ini ada dan mengarah ke routes/api.php
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan middleware cekrole, ionic.role
        $middleware->alias([
            'cekrole' => CekRole::class,
            'ionic.role' => IonicRole::class, // Untuk admin2 dan user API routes
        ]);

        // Terapkan Cors middleware hanya untuk API routes
        // Ini adalah konfigurasi yang benar untuk CORS di Laravel 11
        $middleware->api(prepend: [
            Cors::class, // Middleware CORS Anda akan dijalankan di awal untuk semua rute API
        ]);

        // Middleware lain yang mungkin sudah ada di grup 'web'
        // $middleware->web(append: [
        //     \App\Http\Middleware\HandleInertiaRequests::class,
        //     \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
