<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification; // <--- Tambahkan Model Notification
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

        $status = $request->input('status', 'all');
        
        if ($status === 'unpaid') {
            $query->where('status', 'pending');
        } elseif ($status === 'paid') {
            $query->where('status', 'paid');
        } elseif ($status === 'shipped') {
            $query->where('status', 'shipped');
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%$search%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$search%"));
            });
        }

        $orders = $query->latest()->paginate(10)->appends($request->query());

        $counts = [
            'pending' => Order::where('seller_id', $sellerId)->where('status', 'pending')->count(),
            'paid' => Order::where('seller_id', $sellerId)->where('status', 'paid')->count(),
            'shipped' => Order::where('seller_id', $sellerId)->where('status', 'shipped')->count(),
        ];

        return view('seller.orders.index', compact('orders', 'counts', 'status'));
    }

    public function update(Request $request, Order $order)
    {
        // 1. Validasi Kepemilikan (Security)
        if ($order->seller_id !== Auth::user()->seller->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $action = $request->input('action');

        try {
            return DB::transaction(function () use ($request, $order, $action) {
                
                // --- ACTION: KIRIM BARANG (SHIP) ---
                if ($action === 'ship') {
                    if ($order->status !== 'paid') {
                        throw new \Exception('Hanya pesanan yang sudah dibayar yang bisa dikirim.');
                    }
                    
                    $request->validate([
                        'shipping_resi' => 'required|string|max:50',
                    ]);

                    $order->update([
                        'status' => 'shipped',
                        'shipping_resi' => $request->shipping_resi,
                    ]);

                    // Notifikasi ke Pembeli (Inbox Pattern)
                    Notification::create([
                        'user_id' => $order->user_id,
                        'target'  => 'personal',
                        'type'    => 'info',
                        'title'   => 'Paket Dikirim 🚚',
                        'message' => "Pesanan #{$order->order_code} telah dikirim. Resi: {$request->shipping_resi}"
                    ]);
                    
                    // Flash message (Akan ditangkap partial alert Anda)
                    return back()->with('success', 'Resi berhasil disimpan. Status berubah menjadi Dikirim.');
                }

                // --- ACTION: TOLAK PESANAN (CANCEL) ---
                if ($action === 'cancel') {
                    if (!in_array($order->status, ['pending', 'paid'])) {
                        throw new \Exception('Pesanan yang sudah dikirim atau selesai tidak bisa dibatalkan.');
                    }

                    $order->update(['status' => 'cancelled']);

                    // Kembalikan Stok Produk
                    foreach ($order->details as $detail) {
                        if($detail->product) {
                            $detail->product->increment('stock', $detail->quantity);
                        }
                    }

                    Notification::create([
                        'user_id' => $order->user_id,
                        'target'  => 'personal',
                        'type'    => 'danger',
                        'title'   => 'Pesanan Dibatalkan ❌',
                        'message' => "Mohon maaf, pesanan #{$order->order_code} dibatalkan oleh Penjual."
                    ]);
                    
                    return back()->with('success', 'Pesanan berhasil ditolak & stok dikembalikan.');
                }

                // --- ACTION: SELESAIKAN MANUAL (COMPLETE) ---
                if ($action === 'complete') {
                    if ($order->status !== 'shipped') {
                        throw new \Exception('Pesanan belum dikirim, tidak bisa diselesaikan.');
                    }

                    $order->update([
                        'status' => 'completed',
                        'receive_date' => now(),
                    ]);

                    $order->seller->increment('balance', $order->total_amount);

                    Notification::create([
                        'user_id' => $order->user_id,
                        'target'  => 'personal',
                        'type'    => 'success',
                        'title'   => 'Pesanan Selesai ✅',
                        'message' => "Pesanan #{$order->order_code} telah diselesaikan otomatis."
                    ]);

                    return back()->with('success', 'Pesanan diselesaikan manual. Dana masuk ke saldo.');
                }

                return back()->with('error', 'Aksi tidak dikenali.');
            });

        } catch (\Exception $e) {
            // Flash error (Akan muncul merah di partial alert Anda)
            return back()->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }
}