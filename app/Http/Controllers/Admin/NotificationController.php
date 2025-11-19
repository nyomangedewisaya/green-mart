<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('target', '!=', 'personal')
            ->latest()
            ->paginate(10);
            
        return view('admin.notifications.index', compact('notifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|in:all,sellers,buyers', 
        ]);

        Notification::create([
            'user_id' => null,
            'target' => $request->target,
            'title' => $request->title,
            'message' => $request->message,
            'type' => 'system', 
            'is_read' => false, 
        ]);

        return back()->with('success', 'Pengumuman berhasil diterbitkan.');
    }
}
