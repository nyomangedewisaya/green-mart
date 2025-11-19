<?php

namespace App\Providers;

use App\Models\Seller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $count = Seller::where('is_verified', 0)->count();
            
            $view->with('pendingSellerCount', $count);
        });
    }
}
