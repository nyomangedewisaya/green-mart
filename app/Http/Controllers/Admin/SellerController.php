<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $query = Seller::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%") 
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->status == 'unverified') {
                $query->where('is_verified', false);
            }
        }

        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 10;
        }

        $sellers = $query->latest()->paginate($perPage)->appends($request->query());

        return view('admin.sellers.index', compact('sellers', 'perPageOptions'));
    }

    public function update(Request $request, Seller $seller)
    {
        if ($request->input('action') === 'toggle_status') {
            $newState = !$seller->is_verified;            
            $seller->update(['is_verified' => $newState]);
            $msg = $newState ? 'diaktifkan (Verified)' : 'dinonaktifkan (Unverified)';
            return back()->with('success', "Status toko berhasil $msg.");
        }

        return back()->with('error', 'Admin tidak memiliki akses untuk mengubah data profil seller.');
    }

    public function destroy(Seller $seller)
    {
        if ($seller->logo && !str_starts_with($seller->logo, 'http')) {
             if(file_exists(public_path($seller->logo))) unlink(public_path($seller->logo));
        }
        if ($seller->banner && !str_starts_with($seller->banner, 'http')) {
             if(file_exists(public_path($seller->banner))) unlink(public_path($seller->banner));
        }

        $seller->delete();

        return back()->with('success', 'Toko seller berhasil dihapus.');
    }
}
