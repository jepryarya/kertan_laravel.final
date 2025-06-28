<?php
// app/Http/Middleware/Cors.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class Cors
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CORS Middleware: Request method is ' . $request->method() . ' to path ' . $request->path());

        // Definisikan daftar origin frontend yang diizinkan
        $allowedOrigins = [
            'http://localhost:8100',
            'http://127.0.0.1:8100',
            'capacitor://localhost', // *** PENTING: TAMBAHKAN INI UNTUK PENGUJIAN DI EMULATOR/PERANGKAT FISIK ***
            'ionic://localhost', 
            'https://kertan.hexaboy.com',
        ];

        // Ambil Origin dari request yang masuk
        $requestOrigin = $request->header('Origin');
        $originHeaderValue = '';

        // Periksa apakah Origin dari request ada di dalam daftar yang diizinkan
        if (in_array($requestOrigin, $allowedOrigins)) {
            $originHeaderValue = $requestOrigin;
            Log::info('CORS Middleware: Origin request diizinkan: ' . $requestOrigin);
        } else {
            Log::warning('CORS Middleware: Origin request TIDAK diizinkan: ' . ($requestOrigin ?: 'Tidak ada Origin header') );
            // Jika Origin tidak diizinkan, biarkan $originHeaderValue kosong.
            // Browser akan otomatis memblokir karena tidak ada header ACAO yang cocok.
            // Ini adalah perilaku yang diinginkan untuk keamanan.
        }

        $headers = [
            'Access-Control-Allow-Origin'      => $originHeaderValue, // Nilai ini akan dinamis atau kosong
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, X-Requested-With', // Tambahkan header yang relevan dari Ionic
            'Access-Control-Allow-Credentials' => 'true' // Penting untuk otentikasi berbasis cookie/session
        ];

        // Handle preflight OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            Log::info('CORS Middleware: Handling OPTIONS request.');
            // Jika preflight OPTIONS, langsung kirim respons dengan header CORS
            return response()->json('ok', 200, $headers);
        }

        // Untuk permintaan lainnya, teruskan ke middleware berikutnya dan kemudian tambahkan header CORS ke respons
        $response = $next($request);

        foreach ($headers as $key => $value) {
            // Hanya tambahkan header Access-Control-Allow-Origin jika $originHeaderValue tidak kosong
            // Ini mencegah pengiriman ACAO yang tidak diinginkan jika Origin tidak diizinkan.
            if ($key === 'Access-Control-Allow-Origin' && empty($originHeaderValue)) {
                continue; // Lewati menambahkan header ini jika origin tidak diizinkan
            }
            $response->header($key, $value);
        }
        Log::info('CORS Middleware: Headers applied to response.');

        return $response;
    }
}