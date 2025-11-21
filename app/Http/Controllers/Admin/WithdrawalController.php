<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('seller');

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Cari Nama Seller / Rekening
        if ($request->filled('search')) {
            $query->whereHas('seller', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('account_holder', 'like', '%' . $request->search . '%');
        }
        
        // Statistik Header
        $totalPending = Withdrawal::where('status', 'pending')->sum('amount');
        $totalPaid = Withdrawal::where('status', 'approved')->sum('amount');

        $withdrawals = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.withdrawals.index', compact('withdrawals', 'totalPending', 'totalPaid'));
    }

    public function update(Request $request, Withdrawal $withdrawal)
    {
        $action = $request->input('action');

        if ($withdrawal->status != 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        if ($action == 'approve') {
            // 1. Tandai Approved
            $withdrawal->update([
                'status' => 'approved',
                'admin_note' => 'Transfer Berhasil'
            ]);
            
            // Catatan: Saldo seller biasanya SUDAH dikurangi saat request dibuat di sisi Seller.
            // Jadi disini kita tidak perlu kurangi saldo lagi, cukup ubah status.
            
            return back()->with('success', 'Penarikan disetujui. Dana dianggap sudah ditransfer.');
        
        } elseif ($action == 'reject') {
            // 1. Tandai Rejected
            $withdrawal->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note
            ]);

            // 2. KEMBALIKAN SALDO SELLER
            // Karena request ditolak, uang harus balik ke dompet seller
            $withdrawal->seller->increment('balance', $withdrawal->amount);

            return back()->with('success', 'Penarikan ditolak. Dana dikembalikan ke saldo seller.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}