<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'seller_id' => 'nullable|exists:sellers,id',
            'reason' => 'required|string',
            'description' => 'required|string',
        ]);

        $targetType = null;
        $targetId = null;

        if ($request->has('product_id') && $request->product_id) {
            $targetType = Product::class;
            $targetId = $request->product_id;
        } elseif ($request->has('seller_id') && $request->seller_id) {
            $targetType = Seller::class;
            $targetId = $request->seller_id;
        } else {
            return back()->with('error', 'Target laporan tidak valid.');
        }

        Report::create([
            'user_id' => Auth::id(),
            'target_id' => $targetId,
            'target_type' => $targetType,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan berhasil dikirim ke Admin.');
    }
}
