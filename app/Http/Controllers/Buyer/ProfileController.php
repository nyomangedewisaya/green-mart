<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('buyer.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // --- LOGIKA UPLOAD LANGSUNG KE PUBLIC/AVATARS ---
        if ($request->hasFile('avatar')) {
            $destinationPath = public_path('avatars'); // Folder tujuan: public/avatars

            // 1. Buat folder jika belum ada
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // 2. Hapus avatar lama jika ada (Cek langsung di folder public)
            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            // 3. Pindahkan file baru
            $file = $request->file('avatar');
            $filename = 'avatar-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move($destinationPath, $filename);

            // 4. Simpan path relatif ke database (misal: avatars/namafile.jpg)
            $data['avatar'] = 'avatars/' . $filename;
        }
        // -----------------------------------------------

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
