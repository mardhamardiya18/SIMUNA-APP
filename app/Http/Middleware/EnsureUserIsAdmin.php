<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return redirect()->route('login')->with('error', 'Akses khusus untuk Petugas Puskesmas / Admin.');
        }

        return $next($request);
    }
}
