<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'reason'      => 'required|string|max:100',
            'description' => 'required|string|max:500',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->seller_id !== Auth::user()->seller->id) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        Report::create([
            'user_id'     => Auth::id(),           
            'target_id'   => $order->user_id,      
            'target_type' => User::class,          
            'reason'      => $request->reason,
            'description' => "[Order #{$order->order_code}] " . $request->description, 
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Laporan Anda telah dikirim ke Admin untuk ditinjau.');
    }
}