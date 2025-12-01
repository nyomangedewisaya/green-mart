<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. QUERY UNTUK BUYER (Gunakan user_id, BUKAN seller_id)
        $query = Order::where('user_id', $user->id)
            ->with(['seller', 'details.product']); // Load data Toko dan Produk

        // 2. Filter Status
        $status = $request->input('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // 3. Ambil Data (Paginate)
        $orders = $query->latest()->paginate(10)->appends($request->query());

        // 4. Hitung Badge Counter (Khusus Buyer)
        $counts = [
            'pending' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'paid'    => Order::where('user_id', $user->id)->where('status', 'paid')->count(),
            'shipped' => Order::where('user_id', $user->id)->where('status', 'shipped')->count(),
        ];

        // 5. Return View Buyer
        return view('buyer.orders.index', compact('orders', 'status', 'counts'));
    }

    // --- FUNGSI LAINNYA TETAP SAMA ---

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        return view('buyer.orders.show', compact('order'));
    }

    public function pay(Order $order)
    {
        // Pastikan yang bayar adalah pemilik order
        if ($order->user_id !== Auth::id() || $order->status !== 'pending') abort(403);

        DB::transaction(function() use ($order) {
            $order->update(['status' => 'paid']);

            // Notif ke Seller
            Notification::create([
                'user_id' => $order->seller->user_id,
                'target'  => 'personal',
                'type'    => 'success',
                'title'   => 'Pesanan Dibayar 💰',
                'message' => "Pesanan #{$order->order_code} sudah dibayar oleh pembeli. Segera proses pengiriman."
            ]);
        });

        return back()->with('success', 'Pembayaran berhasil! Menunggu penjual mengirim barang.');
    }

    public function markAsComplete(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'shipped') abort(403);

        DB::transaction(function() use ($order) {
            $order->update([
                'status' => 'completed',
                'receive_date' => now()
            ]);

            // Cairkan Dana ke Seller
            $order->seller->increment('balance', $order->total_amount);

            // Notif ke Seller
            Notification::create([
                'user_id' => $order->seller->user_id,
                'target'  => 'personal',
                'type'    => 'success',
                'title'   => 'Pesanan Selesai ✅',
                'message' => "Pembeli telah menerima pesanan #{$order->order_code}. Dana telah diteruskan ke saldo Anda."
            ]);
        });

        return back()->with('success', 'Terima kasih! Transaksi selesai.');
    }
}