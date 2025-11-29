<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('seller');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('seller', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('account_holder', 'like', '%' . $request->search . '%');
        }
        
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

        return DB::transaction(function () use ($request, $withdrawal, $action) {
            
            if ($action == 'approve') {
                $withdrawal->update([
                    'status' => 'approved',
                    'admin_note' => 'Transfer Berhasil'
                ]);

                Notification::create([
                    'user_id' => $withdrawal->seller->user_id,
                    'target'  => 'personal',
                    'type'    => 'success',
                    'title'   => 'Penarikan Dana Berhasil 💸',
                    'message' => 'Dana sebesar Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' telah disetujui dan ditransfer ke rekening Anda.'
                ]);
                
                return back()->with('success', 'Penarikan disetujui. Dana dianggap sudah ditransfer.');
            
            } elseif ($action == 'reject') {
                $withdrawal->update([
                    'status' => 'rejected',
                    'admin_note' => $request->admin_note
                ]);

                $withdrawal->seller->increment('balance', $withdrawal->amount);

                Notification::create([
                    'user_id' => $withdrawal->seller->user_id,
                    'target'  => 'personal',
                    'type'    => 'danger',
                    'title'   => 'Penarikan Dana Ditolak ❌',
                    'message' => "Permintaan penarikan dana ditolak. Alasan: \"{$request->admin_note}\". Saldo telah dikembalikan ke dompet toko."
                ]);

                return back()->with('success', 'Penarikan ditolak. Dana dikembalikan ke saldo seller.');
            }

            return back()->with('error', 'Aksi tidak valid.');
        });
    }
}