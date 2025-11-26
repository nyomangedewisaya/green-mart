<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'category']);

        // 1. Filter Status
        if ($request->filled('status')) {
            if ($request->status == 'pending') {
                $query->where('is_active', 0);
            } elseif ($request->status == 'live') {
                $query->where('is_active', 1);
            }
        }

        // 2. Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Filter Seller
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }
        
        // 4. Pencarian
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPageOptions = [10, 25, 50, 100, 250];
        $perPage = $request->input('per_page', 10); // Default 10
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 10; // Fallback jika nilainya tidak valid
        }

        // Ambil data dengan paginasi yang dinamis
        $products = $query->latest()->paginate($perPage)->appends($request->query());
        
        // Ambil data untuk <select> filter
        $categories = Category::orderBy('name')->get();
        $sellers = User::where('role', 'seller')->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'sellers', 'perPageOptions'));
    }

    /**
     * Update status produk (Approve, Suspend, Feature, dll)
     */
    public function update(Request $request, Product $product)
    {
        // 'action' akan dikirim dari modal (approve, suspend, feature)
        $action = $request->input('action');

        switch ($action) {
            case 'approve':
                $product->update([
                    'is_active' => 1,
                    'admin_notes' => null 
                ]);
                return back()->with('success', 'Produk berhasil di-Approve.');

            case 'suspend':
                $product->update([
                    'is_active' => 0,
                    'admin_notes' => $request->admin_notes ?? 'Produk ditangguhkan oleh Admin.'
                ]);
                return back()->with('info', 'Produk berhasil di-Suspend.');
                
            case 'reject':
                $product->update([
                    'is_active' => 0,
                    'admin_notes' => $request->admin_notes ?? 'Ditolak tanpa catatan.'
                ]);
                return back()->with('success', 'Produk berhasil di-Reject.');

            case 'toggle_feature':
                $product->update(['is_featured' => !$product->is_featured]);
                $status = $product->is_featured ? 'ditambahkan sebagai' : 'dihapus dari';
                return back()->with('success', "Produk $status Unggulan.");
        }

        return back()->with('error', 'Aksi tidak dikenal.');
    }
}
