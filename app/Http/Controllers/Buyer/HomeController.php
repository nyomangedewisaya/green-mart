<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Banner (Sama seperti sebelumnya)
        $banners = Promotion::where('status', 'paid')->where('is_active', true)->where('start_date', '<=', Carbon::today())->where('end_date', '>=', Carbon::today())->latest()->take(5)->get();

        // 2. Kategori
        $categories = Category::orderBy('name')->get();

        // 3. Produk (PERBAIKAN DISINI)
        $query = Product::with(['category', 'seller', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount('wishlists') // Hitung total wishlist (boleh dilihat guest)
            ->where('is_active', true)
            ->whereNull('admin_notes')
            ->whereHas('seller', function ($q) {
                $q->where('is_verified', true)->whereHas('user', fn($u) => $u->where('status', 'active'));
            });

        if (Auth::check()) {
            $query->withExists([
                'wishlists as is_wishlisted' => function ($q) {
                    $q->where('user_id', Auth::id());
                },
            ]);
        }

        // Filter Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter Sorting
        $sort = $request->input('sort', 'newest');
        $query->orderBy('is_featured', 'desc');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        if (Auth::check()) {
            $query->withExists([
                'wishlists as is_wishlisted' => function ($q) {
                    $q->where('user_id', Auth::id());
                },
            ]);
        }

        $products = $query->paginate(12)->appends($request->query());

        return view('buyer.home', compact('banners', 'categories', 'products'));
    }
}
