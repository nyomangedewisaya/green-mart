<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'buyer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 10;
        }

        $buyers = $query->latest()->paginate($perPage)->appends($request->query());

        return view('admin.buyers.index', compact('buyers', 'perPageOptions'));
    }

    public function update(Request $request, User $buyer)
    {
        if ($buyer->role !== 'buyer') {
            return back()->with('error', 'User ini bukan buyer.');
        }

        if ($request->input('action') === 'toggle_status') {
            $currentStatus = $buyer->status;

            if ($currentStatus === 'active') {
                $newStatus = 'suspended';
                $msg = 'dibekukan (suspended)';
            } else {
                $newStatus = 'active';
                $msg = 'diaktifkan (active)';
            }

            $buyer->update(['status' => $newStatus]);

            return back()->with('success', "Akun buyer berhasil $msg.");
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    public function destroy(User $buyer)
    {
        if ($buyer->role !== 'buyer') {
            return back()->with('error', 'User ini bukan buyer.');
        }

        if ($buyer->avatar && file_exists(public_path('storage/' . $buyer->avatar))) {
            unlink(public_path('storage/' . $buyer->avatar));
        }

        $buyer->delete();

        return back()->with('success', 'Akun buyer berhasil dihapus permanen.');
    }
}
