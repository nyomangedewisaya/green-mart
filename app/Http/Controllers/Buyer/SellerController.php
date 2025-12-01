<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class SellerController extends Controller
{
    public function index(Request $request)
    {
        // Query Dasar
        $query = Seller::where('is_verified', true)->whereHas('user', fn($q) => $q->where('status', 'active'))->withCount('products')->withAvg('reviews', 'rating'); // Hitung Rata-rata Rating Otomatis

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('address', 'like', "%{$search}%");
            });
        }

        // 2. Filter Rating Minimal (Menggunakan Having karena Agregat)
        if ($request->filled('rating')) {
            $query->having('reviews_avg_rating', '>=', $request->rating);
        }

        // 3. Sorting
        $sort = $request->input('sort', 'newest');
        if ($sort == 'oldest') {
            $query->oldest();
        } elseif ($sort == 'products_count') {
            $query->orderBy('products_count', 'desc');
        } elseif ($sort == 'top_rated') {
            $query->orderBy('reviews_avg_rating', 'desc'); // Sort berdasarkan Rating Tertinggi
        } else {
            $query->latest();
        }

        $sellers = $query->paginate(12)->appends($request->query());

        return view('buyer.sellers.index', compact('sellers'));
    }

    public function show(Request $request, $slug)
    {
        // 1. Ambil Data Seller
        $seller = Seller::where('slug', $slug)->firstOrFail();

        // 2. Hitung Statistik Toko (Tetap sama)
        $sellerRating = Review::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->avg('rating');

        $totalReviews = Review::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->count();

        $totalSold = OrderDetail::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->sum('quantity');

        // 3. Ambil Produk Seller (UPDATE BAGIAN INI)
        $query = Product::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->whereNull('admin_notes')
            ->with(['category', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount('wishlists');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Cek status wishlist user yang login
        if (Auth::check()) {
            $query->withExists([
                'wishlists as is_wishlisted' => function ($q) {
                    $q->where('user_id', Auth::id());
                },
            ]);
        }

        $products = $query->latest()->paginate(12)->appends($request->query());

        return view('buyer.sellers.show', compact('seller', 'products', 'sellerRating', 'totalReviews', 'totalSold'));
    }
}
