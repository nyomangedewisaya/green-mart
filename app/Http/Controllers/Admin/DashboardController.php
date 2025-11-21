<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Seller;
use App\Models\Report;
use App\Models\User;
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
        $currentRevenue = Order::whereIn('status', ['paid', 'shipped', 'completed'])
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total_amount');
        $lastMonthRevenue = Order::whereIn('status', ['paid', 'shipped', 'completed'])
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_amount');
        $revenueGrowth = $this->calculateGrowth($currentRevenue, $lastMonthRevenue);
        $currentOrders = Order::where('created_at', '>=', $startOfMonth)->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $orderGrowth = $this->calculateGrowth($currentOrders, $lastMonthOrders);
        $newUsersCount = User::where('created_at', '>=', $startOfMonth)->count();
        $pendingSellers = Seller::where('is_verified', 0)->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $revenueData = [];
        $revenueLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueLabels[] = $date->format('d M');
            $revenueData[] = Order::whereDate('created_at', $date->toDateString())
                ->whereIn('status', ['paid', 'shipped', 'completed'])
                ->sum('total_amount');
        }

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

        $orderStats = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();
            
        $chartStatusData = [
            $orderStats['pending'] ?? 0,
            $orderStats['paid'] ?? 0,
            $orderStats['shipped'] ?? 0,
            $orderStats['completed'] ?? 0,
            $orderStats['cancelled'] ?? 0,
        ];

        $recentOrders = Order::with(['user', 'seller'])->latest()->take(6)->get();
        $topSellers = Seller::withCount(['orders' => function ($query) {
            $query->where('status', 'completed');
        }])->orderByDesc('orders_count')->take(5)->get();

        return view('admin.dashboard', compact(
            'currentRevenue', 'revenueGrowth',
            'currentOrders', 'orderGrowth',
            'newUsersCount',
            'pendingSellers', 'pendingReports', 'pendingOrders',
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