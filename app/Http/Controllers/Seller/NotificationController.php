<?php

namespace App\Http\Controllers\Seller;

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

        $notifications = Notification::query()
            ->where(function ($q) use ($userId) {
                $q->where('target', 'all')
                    ->orWhere('target', 'sellers')
                    ->orWhere(function ($sub) use ($userId) {
                        $sub->where('target', 'personal')->where('user_id', $userId);
                    });
            })
            ->whereDoesntHave('users', function ($q) use ($userId) {
                $q->where('user_id', $userId)->whereNotNull('deleted_at');
            })
            ->with([
                'users' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                },
            ])
            ->latest()
            ->paginate(15);

        return view('seller.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        $notification->users()->syncWithoutDetaching([
            Auth::id() => ['read_at' => now(), 'updated_at' => now()],
        ]);

        return back();
    }

    public function markAllRead()
    {
        $userId = Auth::id();

        $notifIds = Notification::where(function ($q) use ($userId) {
            $q->where('target', 'all')->orWhere('target', 'sellers')->orWhere('user_id', $userId);
        })->pluck('id');

        foreach ($notifIds as $id) {
            DB::table('notification_user')->updateOrInsert(['notification_id' => $id, 'user_id' => $userId], ['read_at' => now(), 'updated_at' => now()]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy($id)
    {
        DB::table('notification_user')->updateOrInsert(['notification_id' => $id, 'user_id' => Auth::id()], ['deleted_at' => now(), 'read_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Notifikasi dihapus.');
    }
}
