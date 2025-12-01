<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil Notifikasi Relevan (Global + Buyers + Personal)
        $notifications = Notification::query()
            ->where(function($q) use ($userId) {
                $q->where('target', 'all')
                  ->orWhere('target', 'buyers') // Target khusus pembeli
                  ->orWhere(function($sub) use ($userId) {
                      $sub->where('target', 'personal')
                          ->where('user_id', $userId);
                  });
            })
            // 2. Filter yang sudah DIHAPUS user ini
            ->whereDoesntHave('users', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereNotNull('deleted_at');
            })
            // 3. Load status 'read_at'
            ->with(['users' => function($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->paginate(15);

        return view('buyer.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        // Sync pivot tanpa menghapus data lain
        $notification->users()->syncWithoutDetaching([
            Auth::id() => ['read_at' => now(), 'updated_at' => now()]
        ]);
        return back();
    }

    public function markAllRead()
    {
        $userId = Auth::id();
        // Ambil semua ID notifikasi buyer
        $notifIds = Notification::where(function($q) use ($userId) {
                $q->where('target', 'all')
                  ->orWhere('target', 'buyers')
                  ->orWhere('user_id', $userId);
            })->pluck('id');

        foreach($notifIds as $id) {
             DB::table('notification_user')->updateOrInsert(
                ['notification_id' => $id, 'user_id' => $userId],
                ['read_at' => now(), 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy($id)
    {
        // Soft Delete di Pivot (Hanya hilang di sisi user ini)
        DB::table('notification_user')->updateOrInsert(
            ['notification_id' => $id, 'user_id' => Auth::id()],
            ['deleted_at' => now(), 'read_at' => now(), 'updated_at' => now()]
        );

        return back()->with('success', 'Notifikasi dihapus.');
    }
}