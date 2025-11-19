<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Seller;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::with('seller');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 10;
        }

        $promotions = $query->latest()->paginate($perPage)->appends($request->query());
        $sellers = Seller::orderBy('name')->get();

        return view('admin.promotions.index', compact('promotions', 'sellers', 'perPageOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'title' => 'required|string|max:100',
            'link' => 'nullable|url|max:255',
            'price' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,paid,expired',
            'is_active' => 'nullable|boolean',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = Str::slug($data['title']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $targetPath = public_path('promotions');
            $file->move($targetPath, $imageName);
            $data['image'] = 'promotions/' . $imageName;
        }

        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        $data['is_active'] = $request->boolean('is_active');

        Promotion::create($data);

        return back()->with('success', 'Promosi baru berhasil ditambahkan.');
    }

    public function update(Request $request, Promotion $promotion)
    {
        $action = $request->input('action');

        if ($action == 'toggle_active') {
            $promotion->update(['is_active' => !$promotion->is_active]);
            $status = $promotion->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Promosi berhasil $status.");
        }

        if ($action == 'update_status') {
            $request->validate([
                'status' => 'required|in:paid',
            ]);
            
            $promotion->update([
                'status' => $request->status,
                'is_active' => true 
            ]);
            return back()->with('success', 'Status promosi telah diubah menjadi Paid.');
        }
        
        $data = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'title' => 'required|string|max:100',
            'link' => 'nullable|url|max:255',
            'price' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,paid,expired',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($promotion->image && file_exists(public_path($promotion->image))) {
                unlink(public_path($promotion->image));
            }
            $file = $request->file('image');
            $imageName = Str::slug($data['title']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $targetPath = public_path('promotions');
            $file->move($targetPath, $imageName);
            $data['image'] = 'promotions/' . $imageName;
        }

        if ($data['title'] !== $promotion->title) {
            $data['slug'] = Str::slug($data['title']) . '-' . $promotion->id;
        }
        $data['is_active'] = $request->boolean('is_active');

        $promotion->update($data);

        return back()->with('success', 'Promosi berhasil diperbarui.');
    }

    public function destroy(Promotion $promotion)
    {
        if ($promotion->image && file_exists(public_path($promotion->image))) {
            unlink(public_path($promotion->image));
        }
        
        $promotion->delete();
        return back()->with('success', 'Promosi berhasil dihapus.');
    }
}