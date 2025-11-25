<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user()->seller;
        $now = Carbon::now();

        // 1. Saldo & Statistik Utama
        $currentBalance = $seller->balance;
        
        $totalEarnings = Order::where('seller_id', $seller->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalWithdrawn = Withdrawal::where('seller_id', $seller->id)
            ->where('status', 'approved')
            ->sum('amount');

        $pendingWithdrawal = Withdrawal::where('seller_id', $seller->id)
            ->where('status', 'pending')
            ->sum('amount');

        // 2. Analisis Pertumbuhan
        $thisMonthRevenue = Order::where('seller_id', $seller->id)
            ->where('status', 'completed')
            ->whereMonth('created_at', $now->month)
            ->sum('total_amount');
            
        $lastMonthRevenue = Order::where('seller_id', $seller->id)
            ->where('status', 'completed')
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->sum('total_amount');

        $growthPercentage = 0;
        if ($lastMonthRevenue > 0) {
            $growthPercentage = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($thisMonthRevenue > 0) {
            $growthPercentage = 100;
        }

        // 3. Data Grafik (7 Hari)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = Order::where('seller_id', $seller->id)
                ->where('status', 'completed')
                ->whereDate('updated_at', $date->toDateString())
                ->sum('total_amount');
        }

        // 4. Riwayat Transaksi (Filter & Merge)
        $filterType = $request->query('type', 'all'); // all, income, expense
        
        $incomes = collect([]);
        $expenses = collect([]);

        // A. Ambil Income (Jika filter 'all' atau 'income')
        if ($filterType == 'all' || $filterType == 'income') {
            $incomes = Order::where('seller_id', $seller->id)
                ->where('status', 'completed')
                ->select(
                    'id', 
                    'order_code as code', 
                    'total_amount as amount', 
                    'updated_at as date', 
                    DB::raw("'income' as type"), 
                    DB::raw("'Penjualan' as description"),
                    DB::raw("NULL as note") // Dummy column agar structure sama
                )
                ->latest('updated_at')
                ->limit(50)
                ->get();
        }

        // B. Ambil Expense (Jika filter 'all' atau 'expense')
        if ($filterType == 'all' || $filterType == 'expense') {
            $expenses = Withdrawal::where('seller_id', $seller->id)
                ->select(
                    'id', 
                    DB::raw("'WD' as code"), 
                    'amount', 
                    'created_at as date', 
                    DB::raw("'expense' as type"), 
                    'status as description',
                    'admin_note as note' // Ambil catatan admin
                )
                ->latest('created_at')
                ->limit(20)
                ->get();
        }

        // Merge & Sort
        $transactions = $incomes->concat($expenses)->sortByDesc('date')->values();

        return view('seller.finance.index', compact(
            'currentBalance', 'totalEarnings', 'totalWithdrawn', 'pendingWithdrawal', 
            'transactions', 'growthPercentage', 'thisMonthRevenue',
            'chartLabels', 'chartData', 'filterType'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|numeric',
            'account_holder' => 'required|string',
        ]);

        $seller = Auth::user()->seller;

        if ($request->amount > $seller->balance) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        DB::transaction(function () use ($request, $seller) {
            Withdrawal::create([
                'seller_id' => $seller->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder' => $request->account_holder,
            ]);
            $seller->decrement('balance', $request->amount);
        });

        return back()->with('success', 'Penarikan diajukan.');
    }
}