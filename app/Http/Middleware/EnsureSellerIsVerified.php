<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role === 'seller') {
            
            if (!$user->seller || !$user->seller->is_verified) {
                
                if ($request->routeIs('seller.status')) {
                    return $next($request);
                }

                return redirect()->route('seller.status');
            }
        }

        return $next($request);
    }
}