<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureBuyerIsVerified;
use App\Http\Middleware\EnsureSellerIsVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class, 
            'seller.verified' => EnsureSellerIsVerified::class, 
            'buyer.verified' => EnsureBuyerIsVerified::class, 
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            $user = Auth::user();
            if (!$user) return route('auth.login');

            if ($user->role === 'admin') return route('admin.dashboard');
            if ($user->role === 'seller') return route('seller.dashboard');
            // if ($user->role === 'buyer') return route('seller.dashboard');
            return route('home');
        });
        $middleware->redirectGuestsTo(fn() => route('auth.login'));
        $middleware->validateCsrfTokens(
            except: [
                'admin/chat/send',
            ],
        );
        $middleware->append(\App\Http\Middleware\UserActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
