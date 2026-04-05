<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Ambil level user yang login
        $userLevel = auth()->user()->level;

        // Cek apakah level user ada di daftar role yang diizinkan
        if (!in_array($userLevel, $roles)) {
            abort(403, 'not found.');
        }

        return $next($request);
    }
}