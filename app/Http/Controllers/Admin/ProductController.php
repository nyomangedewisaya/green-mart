<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'category']);

        if ($request->filled('status')) {
            if ($request->status == 'pending') {
                $query->where('is_active', 0);
            } elseif ($request->status == 'live') {
                $query->where('is_active', 1);
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPageOptions = [10, 25, 50, 100, 250];
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 10; 
        }

        $products = $query->latest()->paginate($perPage)->appends($request->query());
        $categories = Category::orderBy('name')->get();
        $sellers = User::where('role', 'seller')->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'sellers', 'perPageOptions'));
    }

    public function update(Request $request, Product $product)
    {
        $action = $request->input('action');
        $sellerUserId = $product->seller->user_id; 

        switch ($action) {
            case 'approve':
                $product->update([
                    'is_active' => 1,
                    'admin_notes' => null 
                ]);

                Notification::create([
                    'user_id' => $sellerUserId,
                    'target'  => 'personal',
                    'type'    => 'success',
                    'title'   => 'Produk Disetujui ✅',
                    'message' => "Produk \"{$product->name}\" telah disetujui oleh Admin dan sekarang aktif di etalase."
                ]);

                return back()->with('success', 'Produk berhasil di-Approve.');

            case 'suspend':
                $note = $request->admin_notes ?? 'Produk melanggar ketentuan.';
                $product->update([
                    'is_active' => 0,
                    'admin_notes' => $note
                ]);

                Notification::create([
                    'user_id' => $sellerUserId,
                    'target'  => 'personal',
                    'type'    => 'danger',
                    'title'   => 'Produk Ditangguhkan ⚠️',
                    'message' => "Produk \"{$product->name}\" dinonaktifkan oleh Admin. Alasan: {$note}"
                ]);

                return back()->with('info', 'Produk berhasil di-Suspend.');
                
            case 'reject':
                $note = $request->admin_notes ?? 'Tidak memenuhi standar.';
                $product->update([
                    'is_active' => 0,
                    'admin_notes' => $note
                ]);

                Notification::create([
                    'user_id' => $sellerUserId,
                    'target'  => 'personal',
                    'type'    => 'danger',
                    'title'   => 'Produk Ditolak ⛔',
                    'message' => "Pengajuan produk \"{$product->name}\" ditolak. Alasan: {$note}"
                ]);

                return back()->with('success', 'Produk berhasil di-Reject.');

            case 'toggle_feature':
                $product->update(['is_featured' => !$product->is_featured]);
                $status = $product->is_featured ? 'ditambahkan sebagai' : 'dihapus dari';
                
                if($product->is_featured) {
                    Notification::create([
                        'user_id' => $sellerUserId,
                        'target'  => 'personal',
                        'type'    => 'info',
                        'title'   => 'Produk Unggulan 🌟',
                        'message' => "Selamat! Produk \"{$product->name}\" terpilih menjadi Produk Unggulan."
                    ]);
                }

                return back()->with('success', "Produk $status Unggulan.");
        }

        return back()->with('error', 'Aksi tidak dikenal.');
    }
}
