<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        // Ambil hanya yang is_verified = 0
        $query = Seller::with('user')->where('is_verified', 0);

        // Filter Search
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

        // Filter Per Page
        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, $perPageOptions)) $perPage = 10;

        $sellers = $query->oldest()->paginate($perPage)->appends($request->query());

        return view('admin.verification.index', compact('sellers', 'perPageOptions'));
    }

    /**
     * Menyetujui Seller (Approve).
     */
    public function approve(Seller $seller)
    {
        // Ubah jadi verified
        $seller->update(['is_verified' => 1]);

        // Opsional: Kirim email notifikasi ke seller disini

        return back()->with('success', "Toko {$seller->name} berhasil disetujui dan sekarang Aktif.");
    }

    /**
     * Menolak Seller (Reject).
     */
    public function reject(Seller $seller)
    {
        // Hapus file fisik
        if ($seller->logo && !str_starts_with($seller->logo, 'http') && file_exists(public_path($seller->logo))) {
            unlink(public_path($seller->logo));
        }
        // Hapus data (Soft delete atau Hard delete tergantung kebutuhan, disini Hard Delete)
        $seller->delete();
        
        // Opsional: Hapus user loginnya juga jika reject pendaftaran awal
        // $seller->user->delete(); 

        return back()->with('success', "Permohonan toko {$seller->name} telah ditolak dan dihapus.");
    }
}
