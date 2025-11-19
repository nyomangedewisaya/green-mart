<?php

use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [HomeController::class, 'landingPage'])->name('landing');

Route::prefix('auth')->name('auth.')->group(function () {
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

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
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
});

// Route::middleware(['auth', 'seller.approved'])->group(function () {
//     Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');
// });


// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
// });