<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $action = $request->input('action');

        if ($action === 'toggle_status') {
            
            if ($buyer->status === 'active') {
                $request->validate([
                    'admin_note' => 'required|string|max:255'
                ]);
            }

            return DB::transaction(function () use ($request, $buyer) {
                
                if ($buyer->status === 'active') {
                    $buyer->update(['status' => 'suspended']);
                    
                    Notification::create([
                        'user_id' => $buyer->id,
                        'target'  => 'personal',
                        'type'    => 'danger',
                        'title'   => 'Akun Dibekukan ⛔',
                        'message' => "Akun Anda telah dibekukan oleh Admin. Alasan: \"{$request->admin_note}\". Hubungi CS untuk bantuan."
                    ]);
                    
                    return back()->with('success', "Akun buyer berhasil dibekukan (suspended).");
                } 
                else {
                    $buyer->update(['status' => 'active']);

                    Notification::create([
                        'user_id' => $buyer->id,
                        'target'  => 'personal',
                        'type'    => 'success',
                        'title'   => 'Akun Aktif Kembali ✅',
                        'message' => "Selamat! Pembekuan akun Anda telah dicabut. Anda dapat berbelanja kembali."
                    ]);

                    return back()->with('success', "Akun buyer berhasil diaktifkan kembali.");
                }
            });
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