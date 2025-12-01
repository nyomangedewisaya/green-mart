<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\Notification;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        View::composer('layouts.seller', function ($view) {
            if (Auth::check() && Auth::user()->role == 'seller') {
                $userId = Auth::id();

                $totalRelevant = Notification::where(function ($q) use ($userId) {
                    $q->where('target', 'all')
                        ->orWhere('target', 'sellers')
                        ->orWhere(function ($sub) use ($userId) {
                            $sub->where('target', 'personal')->where('user_id', $userId);
                        });
                })->count();

                $totalRead = DB::table('notification_user')->where('user_id', $userId)->whereNotNull('read_at')->whereNull('deleted_at')->count();

                $unreadNotifCount = max(0, $totalRelevant - $totalRead);
                $view->with('unreadNotifCount', $unreadNotifCount);
            }
        });

        View::composer('layouts.buyer', function ($view) {
            if (Auth::check() && Auth::user()->role == 'buyer') {
                $userId = Auth::id();

                $totalRelevant = Notification::where(function ($q) use ($userId) {
                    $q->where('target', 'all')
                        ->orWhere('target', 'buyers')
                        ->orWhere(function ($sub) use ($userId) {
                            $sub->where('target', 'personal')->where('user_id', $userId);
                        });
                })->count();

                $totalReadOrDeleted = DB::table('notification_user')
                    ->where('user_id', $userId)
                    ->where(function ($q) {
                        $q->whereNotNull('read_at')->orWhereNotNull('deleted_at');
                    })
                    ->count();

                $unreadNotifCount = max(0, $totalRelevant - $totalReadOrDeleted);

                $view->with('unreadNotifCount', $unreadNotifCount);
            }
        });

        date_default_timezone_set('Asia/Jakarta');
        config(['app.timezone' => 'Asia/Jakarta']);
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
    }
}
