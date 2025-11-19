<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Eager load relasi yang dibutuhkan
        $query = Order::with(['user', 'seller', 'details.product']);

        // 1. Filter Search (Invoice, Nama Buyer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Filter Seller (BARU: Karena sekarang ada kolom seller_id)
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // 4. Filter Per Page
        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, $perPageOptions)) $perPage = 10;
        
        $orders = $query->latest()->paginate($perPage)->appends($request->query());
        
        // Data untuk dropdown filter
        $sellers = Seller::orderBy('name')->get();

        return view('admin.transactions.index', compact('orders', 'perPageOptions', 'sellers'));
    }
}
