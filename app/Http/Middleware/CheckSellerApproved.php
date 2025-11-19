<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSellerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        $user = Auth::user();

        if ($user->role == 'seller' && $user->status == 'suspended') {
            return redirect()->route('auth.suspended');
        }

        if ($user->role == 'seller' && $user->status == 'active') {
            return $next($request);
        }

        if ($user->role == 'seller' && $user->status == 'pending') {
            return redirect()->route('auth.pending');
        }

        return redirect()->route('home');
    }
}
