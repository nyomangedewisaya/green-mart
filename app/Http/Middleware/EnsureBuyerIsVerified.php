<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureBuyerIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role === 'buyer') {
            if ($user->status === 'suspended') {
                if ($request->routeIs('buyer.suspended') || $request->routeIs('auth.logout')) {
                    return $next($request);
                }

                return redirect()->route('buyer.suspended');
            }

            if ($request->routeIs('buyer.suspended')) {
                return redirect()->route('buyer.home');
            }
        }

        return $next($request);
    }
}
