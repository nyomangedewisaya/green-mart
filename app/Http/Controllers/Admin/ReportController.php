<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'target']);

        if ($request->filled('target_type')) {
            $typeMap = [
                'buyer' => 'App\Models\User',
                'seller' => 'App\Models\Seller',
                'product' => 'App\Models\Product',
            ];

            if (array_key_exists($request->target_type, $typeMap)) {
                $query->where('target_type', $typeMap[$request->target_type]);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $reports = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.reports.index', compact('reports'));
    }

    public function update(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:rejected,resolved',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        $targetName = 'Entitas';
        if ($report->target_type === 'App\Models\User') {
            $targetName = 'Pembeli';
        } elseif ($report->target_type === 'App\Models\Seller') {
            $targetName = 'Toko';
        } elseif ($report->target_type === 'App\Models\Product') {
            $targetName = 'Produk';
        }

        $noteContent = $request->admin_note ? " Catatan Admin: \"{$request->admin_note}\"" : '';
        $reason = $report->reason ?? 'Masalah';

        if ($request->status == 'resolved') {
            Notification::create([
                'user_id' => $report->user_id,
                'target' => 'personal',
                'type' => 'success',
                'title' => 'Laporan Diselesaikan ✅',
                'message' => "Terima kasih. Laporan Anda mengenai {$targetName} dengan alasan \"{$reason}\" telah kami tindak lanjuti.{$noteContent}",
            ]);
        } else {
            Notification::create([
                'user_id' => $report->user_id,
                'target' => 'personal',
                'type' => 'warning',
                'title' => 'Laporan Ditutup 📝',
                'message' => "Laporan Anda mengenai {$targetName} dengan alasan \"{$reason}\" telah kami tinjau namun ditutup/ditolak.{$noteContent}",
            ]);
        }

        return back()->with('success', 'Status laporan diperbarui & notifikasi dikirim ke pelapor.');
    }
}
