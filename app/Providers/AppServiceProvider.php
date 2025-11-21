<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
            $unreadChatCount = 0;
            if (Auth::check()) {
                $unreadChatCount = Chat::where('receiver_id', Auth::id())->where('is_read', false)->count();
            }
            
            $view->with('pendingSellerCount', $count);
            $view->with('unreadChatCount', $unreadChatCount);
        });

        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
    }
}
