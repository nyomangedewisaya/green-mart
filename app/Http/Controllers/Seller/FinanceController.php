<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\SellerBankAccount;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user()->seller;
        $now = Carbon::now();
        $currentBalance = $seller->balance;

        $totalEarnings = Order::where('seller_id', $seller->id)->where('status', 'completed')->sum('total_amount');

        $totalWithdrawn = Withdrawal::where('seller_id', $seller->id)->where('status', 'approved')->sum('amount');

        $pendingWithdrawal = Withdrawal::where('seller_id', $seller->id)->where('status', 'pending')->sum('amount');

        $totalAdsSpent = Promotion::where('seller_id', $seller->id)->where('status', 'paid')->sum('price');

        $thisMonthRevenue = Order::where('seller_id', $seller->id)->where('status', 'completed')->whereMonth('created_at', $now->month)->sum('total_amount');

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

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');

            $income = Order::where('seller_id', $seller->id)->where('status', 'completed')->whereDate('updated_at', $date->toDateString())->sum('total_amount');

            $wd = Withdrawal::where('seller_id', $seller->id)
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('created_at', $date->toDateString())
                ->sum('amount');

            $promo = Promotion::where('seller_id', $seller->id)
                ->whereIn('status', ['pending', 'paid'])
                ->whereDate('created_at', $date->toDateString())
                ->sum('price');

            $chartIncome[] = $income;
            $chartExpense[] = $wd + $promo;
        }

        $filterType = $request->query('type', 'all');
        $incomes = collect([]);
        $expenses = collect([]);

        if ($filterType == 'all' || $filterType == 'expense') {
            // 1. Penarikan Dana (Withdrawal)
            // Tambahkan ->map() untuk memastikan konsistensi struktur object
            $withdrawals = Withdrawal::where('seller_id', $seller->id)
                ->latest('created_at')
                ->limit(20)
                ->get()
                ->map(function ($wd) {
                    // Ubah jadi Object
                    return (object) [
                        'id' => $wd->id,
                        'code' => 'WD',
                        'amount' => $wd->amount,
                        'date' => $wd->created_at,
                        'status' => $wd->status,
                        'bank_name' => $wd->bank_name,
                        'account_number' => $wd->account_number,
                        'account_holder' => $wd->account_holder,
                        'type' => 'expense',
                        'description' => $wd->status, // pending/approved/rejected
                        'note' => $wd->admin_note,
                        'start_date' => null,
                        'end_date' => null,
                        'payment_method' => null, // Tambahan agar tidak error undefined
                    ];
                });

            // 2. Biaya Iklan (Promosi)
            $promotions = Promotion::where('seller_id', $seller->id)
                ->whereIn('status', ['paid', 'pending'])
                ->latest('updated_at')
                ->limit(20)
                ->get()
                ->map(function ($promo) {
                    // ▼▼▼ PERBAIKAN UTAMA: Cast ke (object) ▼▼▼
                    return (object) [
                        'id' => $promo->id,
                        'code' => 'ADS',
                        'amount' => $promo->price,
                        'date' => $promo->updated_at,
                        'start_date' => $promo->start_date,
                        'end_date' => $promo->end_date,
                        'status' => $promo->status,
                        'type' => 'expense',
                        'description' => 'promosi',
                        'note' => $promo->title,
                        'bank_name' => null,
                        'account_number' => null,
                        'account_holder' => null,
                        'payment_method' => null, // Tambahan
                    ];
                });

            $expenses = $withdrawals->concat($promotions);
        }

        // A. Income: Penjualan
        // Kita map juga agar konsisten jadi object standar
        if ($filterType == 'all' || $filterType == 'income') {
            $incomes = Order::where('seller_id', $seller->id)
                ->where('status', 'completed')
                ->with('user')
                ->latest('updated_at')
                ->limit(50)
                ->get()
                ->map(function ($order) {
                    return (object) [
                        'id' => $order->id,
                        'code' => $order->order_code,
                        'amount' => $order->total_amount,
                        'date' => $order->updated_at,
                        'payment_method' => $order->payment_method,
                        'type' => 'income',
                        'description' => 'Penjualan',
                        'note' => null,
                        'status' => 'completed', // Tambahan
                        'start_date' => null,
                        'end_date' => null,
                        'bank_name' => null,
                        'account_number' => null,
                        'account_holder' => null,
                    ];
                });
        }

        $transactions = $incomes->concat($expenses)->sortByDesc('date')->values();
        $savedBanks = $seller->bankAccounts;

        return view('seller.finance.index', compact('currentBalance', 'totalEarnings', 'totalWithdrawn', 'pendingWithdrawal', 'transactions', 'growthPercentage', 'thisMonthRevenue', 'chartLabels', 'chartIncome', 'chartExpense', 'filterType', 'savedBanks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|numeric',
            'account_holder' => 'required|string',
            'save_bank' => 'nullable|boolean',
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

            if ($request->boolean('save_bank')) {
                $exists = SellerBankAccount::where('seller_id', $seller->id)->where('account_number', $request->account_number)->exists();

                if (!$exists) {
                    SellerBankAccount::create([
                        'seller_id' => $seller->id,
                        'bank_name' => $request->bank_name,
                        'account_number' => $request->account_number,
                        'account_holder' => $request->account_holder,
                    ]);
                }
            }
        });

        return back()->with('success', 'Penarikan diajukan.');
    }

    public function destroyBank($id)
    {
        $sellerId = Auth::user()->seller->id;

        $bank = SellerBankAccount::where('id', $id)
            ->where('seller_id', $sellerId)
            ->first();

        if ($bank) {
            $bank->delete();
            return back()->with('success', 'Rekening berhasil dihapus.');
        }

        return back()->with('error', 'Rekening tidak ditemukan.');
    }
}
