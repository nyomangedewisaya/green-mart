<?php

use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\FinanceController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\StatusController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [HomeController::class, 'landingPage'])->name('landing');

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AuthController::class, 'handleLogin'])->name('login.post');
            Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
            Route::post('/register', [AuthController::class, 'handleRegister'])->name('register.post');
        });

        Route::middleware('auth')->group(function () {
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

            Route::get('/pending', [AuthController::class, 'showPendingPage'])
                ->middleware('seller.pending')
                ->name('pending');

            Route::get('/suspended', [AuthController::class, 'showSuspendedPage'])
                ->middleware('seller.suspended')
                ->name('suspended');
        });
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::resource('promotions', PromotionController::class)->except(['show', 'create', 'edit']);
        Route::resource('sellers', SellerController::class)->except(['create', 'store', 'show']);
        Route::get('/verification', [VerificationController::class, 'index'])->name('verification.index');
        Route::put('/verification/{seller}/approve', [VerificationController::class, 'approve'])->name('verification.approve');
        Route::delete('/verification/{seller}/reject', [VerificationController::class, 'reject'])->name('verification.reject');
        Route::resource('/buyers', BuyerController::class)->except(['create', 'store', 'show', 'edit']);
        Route::resource('reports', ReportController::class)->except(['create', 'store', 'edit', 'destroy']);
        Route::resource('notifications', NotificationController::class)->except(['create', 'update', 'edit', 'destroy']);
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/chat', [ChatController::class, 'index'])->name('chats.index');
        Route::get('/chat/history/{user}', [ChatController::class, 'history'])->name('chats.history');
        Route::post('/chat/send', [ChatController::class, 'store'])->name('chats.store');
        Route::delete('/chat/clear/{user}', [ChatController::class, 'clearConversation'])->name('chats.clear');
        Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::put('/withdrawals/{withdrawal}', [WithdrawalController::class, 'update'])->name('withdrawals.update');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'role:seller'])
    ->group(function () {
        Route::get('/status', [StatusController::class, 'index'])->name('status');
        Route::middleware(['seller.verified'])->group(function () {
            Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
            Route::resource('products', SellerProductController::class)->except(['show', 'create', 'edit']);
            Route::resource('orders', OrderController::class)->only(['index', 'update']);
            Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
            Route::post('/finance/withdraw', [FinanceController::class, 'store'])->name('finance.withdraw');
        });
    });

Route::get('/chat/search-user', [ChatController::class, 'searchNewUser'])->name('chats.search_user');

// Route::middleware(['auth', 'seller.approved'])->group(function () {
//     Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');
// });

// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
// });
