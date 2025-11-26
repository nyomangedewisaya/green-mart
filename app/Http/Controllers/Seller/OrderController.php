<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::user()->seller->id;
        
        $query = Order::where('seller_id', $sellerId)
            ->with(['user', 'details.product']);

        // 1. Filter Tab Status (Sesuai Enum DB)
        $status = $request->input('status', 'all');
        
        if ($status === 'unpaid') {
            $query->where('status', 'pending');
        } elseif ($status === 'paid') { // Siap Dikirim
            $query->where('status', 'paid');
        } elseif ($status === 'shipped') {
            $query->where('status', 'shipped');
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        // 2. Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%$search%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$search%"));
            });
        }

        $orders = $query->latest()->paginate(10)->appends($request->query());

        // Hitung Counter untuk Badge Tab
        $counts = [
            'pending' => Order::where('seller_id', $sellerId)->where('status', 'pending')->count(),
            'paid' => Order::where('seller_id', $sellerId)->where('status', 'paid')->count(), // Ini yang perlu dikirim
            'shipped' => Order::where('seller_id', $sellerId)->where('status', 'shipped')->count(),
        ];

        return view('seller.orders.index', compact('orders', 'counts', 'status'));
    }

    // Menggunakan order_code di URL
    public function update(Request $request, Order $order)
    {
        if ($order->seller_id !== Auth::user()->seller->id) abort(403);

        $action = $request->input('action');

        try {
            return DB::transaction(function () use ($request, $order, $action) {
                if ($action === 'ship') {
                    if ($order->status !== 'paid') throw new \Exception('Status order tidak valid.');
                    
                    $request->validate([
                        'shipping_resi' => 'required|string|max:50',
                    ]);

                    $order->update([
                        'status' => 'shipped',
                        'shipping_resi' => $request->shipping_resi,
                    ]);
                    
                    return back()->with('success', 'Resi disimpan. Pesanan dalam pengiriman.');
                }

                if ($action === 'cancel') {
                    if (!in_array($order->status, ['pending', 'paid'])) {
                        throw new \Exception('Tidak bisa membatalkan pesanan yang sudah dikirim/selesai.');
                    }

                    $order->update(['status' => 'cancelled']);

                    foreach ($order->details as $detail) {
                        if($detail->product) {
                            $detail->product->increment('stock', $detail->quantity);
                        }
                    }
                    
                    return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
                }

                // 3. SELESAIKAN (Shipped -> Completed)
                // Opsi manual jika buyer lupa konfirmasi
                if ($action === 'complete') {
                    if ($order->status !== 'shipped') throw new \Exception('Pesanan belum dikirim.');

                    $order->update([
                        'status' => 'completed',
                        'receive_date' => now(),
                    ]);

                    $order->seller->increment('balance', $order->total_amount);

                    return back()->with('success', 'Pesanan diselesaikan manual. Dana masuk ke saldo.');
                }

                return back()->with('error', 'Aksi tidak dikenali.');
            });

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}