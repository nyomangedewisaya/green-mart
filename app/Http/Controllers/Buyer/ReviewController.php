<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            // order_id tetap divalidasi keberadaannya untuk memastikan user beneran beli
            // tapi TIDAK disimpan ke tabel reviews
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // 1. Validasi: Pastikan User benar-benar pernah membeli produk ini di order yang sudah selesai
        // Kita cek apakah ada order milik user ini, status completed, dan mengandung produk tsb
        $hasPurchased = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('details', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'Anda belum membeli produk ini atau pesanan belum selesai.');
        }

        // 2. Cek Duplikasi: Apakah user sudah pernah mereview produk ini sebelumnya?
        // (Cek berdasarkan user_id dan product_id saja, KARENA tidak ada order_id di tabel review)
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah pernah mengulas produk ini.');
        }

        // 3. Simpan Review (Tanpa order_id)
        Review::create([
            'user_id'    => $user->id,
            'product_id' => $request->product_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda telah diterbitkan.');
    }
}