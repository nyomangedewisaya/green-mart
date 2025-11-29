<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Promotion;
use App\Models\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        Promotion::where('end_date', '<', Carbon::today())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired', 'is_active' => false]);

        $activeSlots = Promotion::where('status', 'paid')->where('is_active', true)->where('end_date', '>=', Carbon::today())->count();

        $maxSlots = 25;
        $slotPercentage = ($activeSlots / $maxSlots) * 100;

        $pendingPromotions = Promotion::where('status', 'pending')->with('seller')->orderBy('created_at', 'asc')->get();

        $query = Promotion::with('seller');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%$search%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        $promotions = $query->latest()->paginate($request->input('per_page', 10));
        $sellers = User::where('role', 'seller')->get();

        return view('admin.promotions.index', compact('promotions', 'sellers', 'pendingPromotions', 'activeSlots', 'maxSlots', 'slotPercentage'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $action = $request->input('action');

        if ($action === 'update_status') {
            $status = $request->input('status'); 

            $promotion->update([
                'status' => $status,
                'is_active' => $status === 'paid', 
            ]);

            if ($status === 'paid') {
                Notification::create([
                    'user_id' => $promotion->seller->user_id, 
                    'target' => 'personal',
                    'type' => 'success',
                    'title' => 'Promosi Disetujui! 📢',
                    'message' => "Selamat! Iklan \"{$promotion->title}\" telah disetujui dan sekarang sedang tayang.",
                ]);
            } elseif ($status === 'expired') {
                Notification::create([
                    'user_id' => $promotion->seller->user_id,
                    'target' => 'personal',
                    'type' => 'warning',
                    'title' => 'Promosi Berakhir ⏳',
                    'message' => "Masa tayang iklan \"{$promotion->title}\" telah berakhir.",
                ]);
            }

            return back()->with('success', 'Status promosi diperbarui.');
        }

        if ($action === 'toggle_active') {
            $promotion->update(['is_active' => !$promotion->is_active]);

            if (!$promotion->is_active) {
                Notification::create([
                    'user_id' => $promotion->seller->user_id,
                    'target' => 'personal',
                    'type' => 'danger',
                    'title' => 'Iklan Dinonaktifkan Admin ⚠️',
                    'message' => "Iklan \"{$promotion->title}\" dinonaktifkan sementara oleh Admin karena alasan kebijakan.",
                ]);
            }

            return back()->with('success', 'Status aktif promosi diubah.');
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
