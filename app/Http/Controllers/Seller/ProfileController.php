<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $seller = $user->seller;
        return view('seller.profile.index', compact('user', 'seller'));
    }

    public function updateStore(Request $request)
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data = $request->only(['name', 'phone', 'address', 'description']);

        if ($seller->name !== $request->name) {
            $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        }

        if ($request->hasFile('logo')) {
            if ($seller->logo && File::exists(public_path($seller->logo))) {
                File::delete(public_path($seller->logo));
            }
            $file = $request->file('logo');
            $filename = 'logo-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sellers'), $filename);
            $data['logo'] = 'uploads/sellers/' . $filename;
        }

        if ($request->hasFile('banner')) {
            if ($seller->banner && File::exists(public_path($seller->banner))) {
                File::delete(public_path($seller->banner));
            }
            $file = $request->file('banner');
            $filename = 'banner-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sellers'), $filename);
            $data['banner'] = 'uploads/sellers/' . $filename;
        }

        $seller->update($data);

        return back()->with('success', 'Profil toko berhasil diperbarui.');
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Informasi akun berhasil diperbarui.');
    }
}
