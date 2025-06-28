<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/welcome')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            return redirect('/welcome')->withErrors(['akses' => 'Akses ditolak: tidak memiliki izin.']);
        }

        return $next($request);
    }
}
