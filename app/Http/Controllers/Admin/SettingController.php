<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
        // Ambil data setting yang ada di DB
        $setting = Setting::first();

        // Jika belum ada, buat baru
        if (!$setting) {
            $setting = new Setting();
        }

        // 1. Validasi Input
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

            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'site_favicon' => 'nullable|image|mimes:png,ico|max:1024',
        ]);

        // ▼▼▼ PERUBAHAN DISINI: Langsung di folder 'settings' dalam public ▼▼▼
        $uploadPath = public_path('settings');

        // Pastikan folder 'public/settings' ada
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // --- 2. Logic Upload Logo ---
        if ($request->hasFile('site_logo')) {
            // Hapus file lama jika ada
            if ($setting->site_logo && File::exists(public_path($setting->site_logo))) {
                File::delete(public_path($setting->site_logo));
            }

            $file = $request->file('site_logo');
            $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();

            // Pindahkan file ke public/settings
            $file->move($uploadPath, $filename);

            // Simpan path relatif ke database
            $data['site_logo'] = 'settings/' . $filename;
        }

        // --- 3. Logic Upload Favicon ---
        if ($request->hasFile('site_favicon')) {
            // Hapus file lama jika ada
            if ($setting->site_favicon && File::exists(public_path($setting->site_favicon))) {
                File::delete(public_path($setting->site_favicon));
            }

            $file = $request->file('site_favicon');
            $filename = 'favicon-' . time() . '.' . $file->getClientOriginalExtension();

            // Pindahkan file
            $file->move($uploadPath, $filename);

            // Simpan path relatif
            $data['site_favicon'] = 'settings/' . $filename;
        }
        // ▲▲▲ ------------------------------------------------------- ▲▲▲

        // 4. Simpan Perubahan
        $setting->fill($data);
        $setting->save();

        return back()->with('success', 'Identitas website berhasil diperbarui.');
    }
}
