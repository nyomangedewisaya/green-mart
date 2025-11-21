<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil data pertama. Jika belum ada (karena lupa seed), buat instance baru kosong.
        $setting = Setting::first() ?? new Setting();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        // Ambil data setting yang ada
        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting(); // Handle safety jika kosong
        }

        // Validasi Input
        $data = $request->validate([
            'site_name' => 'required|string|max:50',
            'site_tagline' => 'nullable|string|max:150',
            'site_description' => 'nullable|string|max:500',
            
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string',
            
            'link_facebook' => 'nullable|url',
            'link_instagram' => 'nullable|url',
            'link_twitter' => 'nullable|url',
            
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048', // Max 2MB
            'site_favicon' => 'nullable|image|mimes:png,ico|max:1024', // Max 1MB
        ]);

        // --- Logic Upload Logo ---
        if ($request->hasFile('site_logo')) {
            // Hapus logo lama jika ada dan file-nya eksis
            if ($setting->site_logo && file_exists(public_path($setting->site_logo))) {
                unlink(public_path($setting->site_logo));
            }
            
            $file = $request->file('site_logo');
            $name = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            // Simpan di folder public/uploads/settings
            $file->move(public_path('uploads/settings'), $name);
            $data['site_logo'] = 'uploads/settings/' . $name;
        }

        // --- Logic Upload Favicon ---
        if ($request->hasFile('site_favicon')) {
            if ($setting->site_favicon && file_exists(public_path($setting->site_favicon))) {
                unlink(public_path($setting->site_favicon));
            }
            
            $file = $request->file('site_favicon');
            $name = 'favicon-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $name);
            $data['site_favicon'] = 'uploads/settings/' . $name;
        }

        // Simpan ke Database
        $setting->fill($data);
        $setting->save();

        return back()->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}