<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat; 
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $myId = Auth::id();

        // 1. Ambil User untuk Sidebar
        // HANYA user yang punya chat yang BELUM dihapus oleh saya (Admin)
        $users = User::whereIn('id', function($query) use ($myId) {
            // Subquery: Ambil ID pengirim pesan yang masuk ke saya, dan belum saya hapus
            $query->select('sender_id')
                  ->from('chats')
                  ->where('receiver_id', $myId)
                  ->where('deleted_by_receiver', false) // PENTING: Filter yang belum dihapus
                  
                  ->union(
                      // Subquery: Ambil ID penerima pesan yang saya kirim, dan belum saya hapus
                      $query->newQuery()->select('receiver_id')
                            ->from('chats')
                            ->where('sender_id', $myId)
                            ->where('deleted_by_sender', false) // PENTING: Filter yang belum dihapus
                  );
        })
        ->get()
        ->map(function ($user) use ($myId) {
            // Cek Online (5 menit terakhir)
            $user->is_online = $user->last_seen && Carbon::parse($user->last_seen)->gt(now()->subMinutes(5));
            
            // Hitung Unread (Hanya yang belum dihapus)
            $user->unread_count = Chat::where('sender_id', $user->id)
                ->where('receiver_id', $myId)
                ->where('is_read', false)
                ->where('deleted_by_receiver', false) // PENTING
                ->count();

            // Ambil pesan terakhir (Hanya yang belum dihapus) untuk sorting
            $lastMsg = Chat::where(function($q) use ($user, $myId){
                 $q->where('sender_id', $user->id)
                   ->where('receiver_id', $myId)
                   ->where('deleted_by_receiver', false);
             })->orWhere(function($q) use ($user, $myId){
                 $q->where('sender_id', $myId)
                   ->where('receiver_id', $user->id)
                   ->where('deleted_by_sender', false);
             })->latest()->first();
             
            $user->last_msg_time = $lastMsg ? $lastMsg->created_at : null;
            
            return $user;
        })
        ->sortByDesc('last_msg_time')
        ->values();

        return view('admin.chats.index', compact('users'));
    }

    // API: History Chat (Isi Pesan)
    public function history(User $user)
    {
        $myId = Auth::id();

        // Tandai Read
        Chat::where('sender_id', $user->id)
            ->where('receiver_id', $myId)
            ->update(['is_read' => true]);

        // Ambil chat yang BELUM dihapus oleh saya
        $chats = Chat::where(function ($q) use ($myId, $user) {
                // Pesan masuk: Cek deleted_by_receiver
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $myId)
                  ->where('deleted_by_receiver', false);
            })
            ->orWhere(function ($q) use ($myId, $user) {
                // Pesan keluar: Cek deleted_by_sender
                $q->where('sender_id', $myId)
                  ->where('receiver_id', $user->id)
                  ->where('deleted_by_sender', false);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($chats);
    }

    // API: Hapus 1 Pesan
    public function deleteMessage(Chat $chat)
    {
        $myId = Auth::id();

        // Update flag deleted sesuai peran user (sender/receiver)
        if ($chat->sender_id == $myId) {
            $chat->update(['deleted_by_sender' => true]);
        } elseif ($chat->receiver_id == $myId) {
            $chat->update(['deleted_by_receiver' => true]);
        }

        // Jika dua-duanya sudah hapus, hapus permanen dari DB
        if ($chat->refresh()->deleted_by_sender && $chat->deleted_by_receiver) {
            $chat->delete();
        }

        return response()->json(['status' => 'success']);
    }

    public function searchNewUser(Request $request)
    {
        $search = $request->query('q');
        $type = $request->query('type', 'all');

        $query = User::where('id', '!=', Auth::id())->where('name', 'like', "%{$search}%");

        if ($type !== 'all') {
            $query->where('role', $type);
        }

        $users = $query
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $user->is_online = $user->last_seen && Carbon::parse($user->last_seen)->gt(now()->subMinutes(5));
                return $user;
            });

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json($chat);
    }

    // API: Hapus Seluruh Percakapan
    public function clearConversation(User $user)
    {
        $myId = \Illuminate\Support\Facades\Auth::id();

        Chat::where('sender_id', $myId)
            ->where('receiver_id', $user->id)
            ->update(['deleted_by_sender' => true]);

        Chat::where('sender_id', $user->id)
            ->where('receiver_id', $myId)
            ->update(['deleted_by_receiver' => true]);

        Chat::where('deleted_by_sender', true)
            ->where('deleted_by_receiver', true)
            ->delete();

        return response()->json(['status' => 'cleared']);
    }
}
