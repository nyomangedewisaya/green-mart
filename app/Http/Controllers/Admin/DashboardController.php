<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        // --- 1. PENDAPATAN (Dengan Tren) ---
        $currentRevenue = Order::whereIn('status', ['paid', 'shipped', 'completed'])
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total_amount');
            
        $lastMonthRevenue = Order::whereIn('status', ['paid', 'shipped', 'completed'])
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_amount');

        $revenueGrowth = $this->calculateGrowth($currentRevenue, $lastMonthRevenue);

        // --- 2. TOTAL ORDER (Dengan Tren) ---
        $currentOrders = Order::where('created_at', '>=', $startOfMonth)->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $orderGrowth = $this->calculateGrowth($currentOrders, $lastMonthOrders);

        // --- 3. USER BARU (Buyer + Seller) ---
        $newUsersCount = User::where('created_at', '>=', $startOfMonth)->count();
        
        // --- 4. ACTION ITEMS (Tugas Admin) ---
        $pendingSellers = Seller::where('is_verified', 0)->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $pendingOrders = Order::where('status', 'pending')->count(); // Order belum bayar lama

        // --- 5. DATA GRAFIK ---
        $revenueData = [];
        $revenueLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueLabels[] = $date->format('d M');
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

        // --- 6. DATA TABEL ---
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
            'recentOrders', 'topSellers'
        ));
    }

    // Helper hitung persentase
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }
}