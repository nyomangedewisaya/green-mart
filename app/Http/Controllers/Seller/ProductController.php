<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::user()->seller->id;
        $query = Product::where('seller_id', $sellerId)->with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', 1)->whereNull('admin_notes');
            }
            if ($request->status == 'inactive') {
                $query->where('is_active', 0)->whereNull('admin_notes');
            }
            if ($request->status == 'suspended') {
                $query->whereNotNull('admin_notes');
            }
        }

        $perPage = $request->input('per_page', 10);

        $products = $query
            ->withCount('wishlists')
            ->with(['category', 'reviews.user'])
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());
        $categories = Category::all();

        return view('seller.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'discount' => 'nullable|integer|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['seller_id'] = Auth::user()->seller->id;
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'prod-' . time() . '.' . $file->getClientOriginalExtension();
            $path = public_path('products');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $file->move($path, $filename);
            $data['image'] = 'products/' . $filename;
        }

        Product::create($data);

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        if ($product->seller_id !== Auth::user()->seller->id) {
            abort(403);
        }

        // 2. Handle Quick Status Toggle (Dari Modal Status di Tabel)
        if ($request->has('update_type') && $request->update_type == 'status_toggle') {
            // LOGIKA SUSPEND: Jika ada catatan admin, DILARANG mengaktifkan
            if ($product->admin_notes && $request->boolean('is_active')) {
                return back()->with('error', 'Produk sedang disuspend oleh Admin. Perbaiki data terlebih dahulu atau hubungi Admin.');
            }

            $product->update([
                'is_active' => $request->boolean('is_active'),
            ]);

            $status = $request->boolean('is_active') ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Produk berhasil $status.");
        }

        // 3. Handle Full Edit (Dari Modal Edit)
        $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'discount' => 'nullable|integer|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->except(['image']);

        // Update Slug jika nama berubah
        if ($request->name !== $product->name) {
            $data['slug'] = Str::slug($request->name) . '-' . $product->id;
        }

        // LOGIKA SUSPEND DI EDIT:
        // Jika produk disuspend, paksa status tetap NONAKTIF meskipun seller mencentang "Aktif"
        if ($product->admin_notes) {
            $data['is_active'] = 0;
            // Opsional: Beri flash message khusus
            session()->flash('error', 'Data diperbarui, namun status tetap Nonaktif karena produk sedang disuspend Admin.');
        } else {
            $data['is_active'] = $request->boolean('is_active');
        }

        // Update Image
        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
            $file = $request->file('image');
            $filename = 'prod-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('products'), $filename);
            $data['image'] = 'products/' . $filename;
        }

        $product->update($data);

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->seller_id !== Auth::user()->seller->id) {
            abort(403);
        }

        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
