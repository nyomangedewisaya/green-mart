<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Seller;
use App\Models\Report;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // --- 1. Revenue & Growth ---
        $currentRevenue = Order::whereIn('status', ['paid', 'shipped', 'completed'])
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total_amount');
            
        $lastMonthRevenue = Order::whereIn('status', ['paid', 'shipped', 'completed'])
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_amount');

        $revenueGrowth = $this->calculateGrowth($currentRevenue, $lastMonthRevenue);

        // --- 2. Orders & Growth ---
        $currentOrders = Order::where('created_at', '>=', $startOfMonth)->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $orderGrowth = $this->calculateGrowth($currentOrders, $lastMonthOrders);

        // --- 3. Average Order Value (AOV) & User ---
        $aov = $currentOrders > 0 ? $currentRevenue / $currentOrders : 0;
        $newUsersCount = User::where('created_at', '>=', $startOfMonth)->count();

        // --- 4. Action Center (Pending Items) ---
        $pendingSellers = Seller::where('is_verified', 0)->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        
        // Hitung total untuk badge merah di widget
        $totalActionNeeded = $pendingSellers + $pendingReports + $pendingOrders + $pendingWithdrawals;

        // --- 5. Grafik ---
        $revenueData = [];
        $revenueLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueLabels[] = $date->translatedFormat('d M'); 
            $revenueData[] = Order::whereDate('created_at', $date->toDateString())
                ->whereIn('status', ['paid', 'shipped', 'completed'])
                ->sum('total_amount');
        }

        $orderStats = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();
            
        $chartStatusData = [
            $orderStats['pending'] ?? 0,
            $orderStats['paid'] ?? 0,
            $orderStats['shipped'] ?? 0,
            $orderStats['completed'] ?? 0,
            $orderStats['cancelled'] ?? 0,
        ];

        // --- 6. Tabel Data ---
        $recentOrders = Order::with(['user', 'seller'])->latest()->take(6)->get();

        $topSellers = Seller::withCount(['orders' => function ($query) {
            $query->where('status', 'completed');
        }])
        ->orderByDesc('orders_count')
        ->take(5)
        ->get();

        $topProducts = OrderDetail::select('product_id', DB::raw('sum(quantity) as total_sold'))
            ->whereHas('order', function($q) {
                $q->whereIn('status', ['paid', 'shipped', 'completed']);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'currentRevenue', 'revenueGrowth',
            'currentOrders', 'orderGrowth',
            'newUsersCount', 'aov',
            'pendingSellers', 'pendingReports', 'pendingOrders', 'pendingWithdrawals', 'totalActionNeeded',
            'revenueLabels', 'revenueData', 'chartStatusData',
            'recentOrders', 'topSellers', 'topProducts'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }
}   