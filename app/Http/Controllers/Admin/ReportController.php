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
        $query = Report::with(['user', 'product', 'seller']);

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            });
        }

        $reports = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.reports.index', compact('reports'));
    }

    public function update(Request $request, Report $report)
    {
        $request->validate(['status' => 'required|in:rejected,resolved']);
        
        $report->update(['status' => $request->status]);

        $title = $request->status == 'resolved' ? 'Laporan Diterima' : 'Laporan Ditolak';
        $message = $request->status == 'resolved' 
            ? 'Terima kasih. Laporan Anda telah kami proses dan selesaikan.' 
            : 'Laporan Anda telah kami tinjau namun tidak ditemukan pelanggaran.';
            
        Notification::send($report->user_id, $title, $message, 'report');

        return back()->with('success', 'Status laporan diperbarui & notifikasi dikirim ke pelapor.');
    }
}
