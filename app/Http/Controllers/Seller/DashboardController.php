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

        // --- 1. STATISTIK UTAMA ---
        $completedOrders = Order::where('seller_id', $sellerId)->where('status', 'completed')->count();
        $totalOrders = Order::where('seller_id', $sellerId)->where('status', '!=', 'cancelled')->count();
        $activeProducts = Product::where('seller_id', $sellerId)->where('is_active', 1)->count();
        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $ordersToShip = Order::where('seller_id', $sellerId)->where('status', 'paid')->count();

        // --- 2. PENDAPATAN & PERTUMBUHAN (GROWTH) ---
        $currentMonthRevenue = Order::where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->whereMonth('updated_at', $now->month)
            ->whereYear('updated_at', $now->year)
            ->sum('total_amount');
            
        $lastMonthRevenue = Order::where('seller_id', $sellerId)
            ->where('status', 'completed')
            ->whereMonth('updated_at', $now->subMonth()->month) // Hati-hati dgn subMonth() di objek $now
            ->whereYear('updated_at', $now->year) // Asumsi tahun sama, logika bisa diperkompleks jika lintas tahun
            ->sum('total_amount');

        // Total Revenue Seumur Hidup
        $totalRevenue = Order::where('seller_id', $sellerId)->where('status', 'completed')->sum('total_amount');

        // Hitung Persentase
        $percentageChange = 0;
        if ($lastMonthRevenue > 0) {
            $percentageChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($currentMonthRevenue > 0) {
            $percentageChange = 100; // Jika bulan lalu 0 dan sekarang ada, naik 100%
        }

        // --- 3. DATA CHART (MINGGUAN & BULANAN) ---
        
        // A. MINGGUAN (7 Hari Terakhir)
        $weekLabels = [];
        $weekData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i); // Gunakan Carbon::now() baru agar tidak mutable
            $weekLabels[] = $date->locale('id')->isoFormat('dd, D/M'); // Senin, 20/11
            $weekData[] = Order::where('seller_id', $sellerId)
                ->where('status', 'completed')
                ->whereDate('updated_at', $date->toDateString())
                ->sum('total_amount');
        }

        // B. BULANAN (12 Bulan Tahun Ini)
        $monthLabels = [];
        $monthData = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = Carbon::createFromDate(Carbon::now()->year, $i, 1);
            $monthLabels[] = $date->locale('id')->isoFormat('MMM'); // Jan, Feb
            $monthData[] = Order::where('seller_id', $sellerId)
                ->where('status', 'completed')
                ->whereYear('updated_at', Carbon::now()->year)
                ->whereMonth('updated_at', $i)
                ->sum('total_amount');
        }

        // --- 4. DATA RATING DETAIL ---
        // Mengambil rata-rata
        $ratingAvg = \App\Models\Review::whereHas('product', fn($q) => $q->where('seller_id', $sellerId))->avg('rating') ?? 0;
        
        // Mengambil jumlah per bintang (5, 4, 3, 2, 1)
        $ratingCounts = \App\Models\Review::whereHas('product', fn($q) => $q->where('seller_id', $sellerId))
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray();
        
        // Normalisasi array agar index 1-5 selalu ada
        $ratingDist = [];
        $totalReviews = array_sum($ratingCounts);
        for($i=5; $i>=1; $i--) {
            $count = $ratingCounts[$i] ?? 0;
            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            $ratingDist[$i] = ['count' => $count, 'percentage' => $percentage];
        }

        // --- 5. TOP PRODUCTS & RECENT ORDERS ---
        $topProducts = OrderDetail::select('product_id', DB::raw('sum(quantity) as total_sold'))
            ->whereHas('product', fn($q) => $q->where('seller_id', $sellerId))
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->with('product')
            ->groupBy('product_id')->orderByDesc('total_sold')->take(5)->get();

        $recentOrders = Order::where('seller_id', $sellerId)->with('user')->latest()->take(5)->get();

        return view('seller.dashboard', compact(
            'totalRevenue', 'completedOrders', 'totalOrders', 'activeProducts', 'totalProducts', 'ordersToShip',
            'ratingAvg', 'ratingDist', 'totalReviews',
            'weekLabels', 'weekData', 'monthLabels', 'monthData', 
            'percentageChange', 'currentMonthRevenue',
            'topProducts', 'recentOrders'
        ));
    }
}