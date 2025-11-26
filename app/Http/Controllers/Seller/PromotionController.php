<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::user()->seller->id;
        $query = Promotion::where('seller_id', $sellerId);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 6);
        $promotions = $query->latest()->paginate($perPage)->appends($request->query());

        return view('seller.promotions.index', compact('promotions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'link' => 'nullable|url',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        if ($days > 30) {
            return back()->withInput()->with('error', 'Maksimal durasi promosi adalah 30 hari.');
        }

        $pricePerDay = 25000;
        $totalPrice = $days * $pricePerDay;

        if (Auth::user()->seller->balance < $totalPrice) {
            return back()->withInput()->with('error', 'Saldo tidak mencukupi untuk durasi tersebut.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = public_path('promotions');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $file = $request->file('image');
            $filename = 'promo-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);
            $imagePath = 'promotions/' . $filename;
        }

        DB::transaction(function () use ($request, $totalPrice, $imagePath) {
            Promotion::create([
                'seller_id' => Auth::user()->seller->id,
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . uniqid(),
                'link' => $request->link,
                'image' => $imagePath,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'price' => $totalPrice,
                'status' => 'pending',
                'is_active' => false,
            ]);

            Auth::user()->seller->decrement('balance', $totalPrice);
        });

        return back()->with('success', 'Pengajuan berhasil & Saldo terpotong. Menunggu persetujuan Admin.');
    }
}
