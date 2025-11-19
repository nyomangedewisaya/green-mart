<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'seller.pending'   => \App\Http\Middleware\CheckSellerPending::class,
            'seller.approved'  => \App\Http\Middleware\CheckSellerApproved::class,
            'seller.suspended' => \App\Http\Middleware\CheckSellerSuspended::class, 

            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
