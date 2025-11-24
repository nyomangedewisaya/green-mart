<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $seller = $user->seller;

        if ($user->status === 'approved' && $seller && $seller->is_verified) {
            return redirect()->route('seller.dashboard');
        }

        $statusType = 'pending';
        
        if ($user->status === 'suspended') {
            $statusType = 'suspended';
        } elseif (!$seller->is_verified) {
            $statusType = 'pending_verification'; 
        }

        return view('seller.status', compact('user', 'statusType'));
    }
}