<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user()->seller;
        $sellerId = $seller->id;
        $now = Carbon::now();

        // 1. PENDAPATAN (Netto - Completed)
        $totalRevenue = Order::where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->sum('total_amount');

        // 2. STATISTIK ORDER (Completed / Total)
        $completedOrders = Order::where('seller_id', $sellerId)->where('status', 'completed')->count();
        $totalOrders = Order::where('seller_id', $sellerId)->count(); // Semua status termasuk cancel

        // 3. STATISTIK PRODUK (Active / Total)
        $activeProducts = Product::where('seller_id', $sellerId)->where('is_active', 1)->count();
        $totalProducts = Product::where('seller_id', $sellerId)->count();

        // 4. RATING
        $ratingAvg = \App\Models\Review::whereHas('product', function($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->avg('rating') ?? 0;

        // 5. ACTION CENTER (To Ship)
        $ordersToShip = Order::where('seller_id', $sellerId)->where('status', 'paid')->count();

        // 6. GRAFIK PENDAPATAN
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = Order::where('seller_id', $sellerId)
                ->where('status', 'completed')
                ->whereDate('updated_at', $date->toDateString())
                ->sum('total_amount');
        }

        // 7. STATUS ORDER CHART
        $orderStats = Order::where('seller_id', $sellerId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();
        
        $chartStatusData = [
            $orderStats['pending'] ?? 0,
            $orderStats['paid'] ?? 0,
            $orderStats['shipped'] ?? 0,
            $orderStats['completed'] ?? 0,
            $orderStats['cancelled'] ?? 0,
        ];

        // 8. DATA TABEL
        $topProducts = OrderDetail::select('product_id', DB::raw('sum(quantity) as total_sold'))
            ->whereHas('product', fn($q) => $q->where('seller_id', $sellerId))
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)->get();

        $recentOrders = Order::where('seller_id', $sellerId)
            ->with('user')
            ->latest()
            ->take(5)->get();

        return view('seller.dashboard', compact(
            'totalRevenue', 
            'completedOrders', 'totalOrders', // Kirim 2 variabel ini
            'activeProducts', 'totalProducts', // Kirim 2 variabel ini
            'ratingAvg', 'ordersToShip',
            'chartLabels', 'chartData', 'chartStatusData',
            'topProducts', 'recentOrders'
        ));
    }
}