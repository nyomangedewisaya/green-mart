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
        $query = Product::with(['category', 'seller', 'reviews.user']) // Load reviews & user untuk modal
            ->withAvg('reviews', 'rating') // Hitung rata-rata rating (reviews_avg_rating)
            ->withCount('reviews') // Hitung jumlah review (reviews_count)
            ->where('is_active', true)
            ->whereNull('admin_notes')
            ->withCount('wishlists');

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

        $products = $query->paginate(12)->appends($request->query());

        return view('buyer.home', compact('banners', 'categories', 'products'));
    }
}
