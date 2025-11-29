<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'reason' => 'required|string',
            'description' => 'required|string',
        ]);

        Report::create([
            'user_id' => Auth::id(),
            'target_id' => $request->product_id,
            'target_type' => Product::class, // Laporin Produk
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Laporan berhasil dikirim ke Admin.');
    }
}