<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user()->seller;
        $sellerId = $seller->id;

        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $totalOrders = Order::where('seller_id', $sellerId)->count();
        
        $totalRevenue = Order::where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->sum('total_amount');

        $recentOrders = Order::where('seller_id', $sellerId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact('totalProducts', 'totalOrders', 'totalRevenue', 'recentOrders'));
    }
}