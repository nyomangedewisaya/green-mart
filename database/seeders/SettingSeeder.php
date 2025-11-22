<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu agar tidak duplikat
        Setting::truncate();

        Setting::create([
            // Identitas
            'site_name' => 'Green Mart',
            'site_tagline' => 'Belanja Segar, Hidup Sehat',
            'site_description' => 'Marketplace terpercaya untuk kebutuhan sayur, buah, dan bahan dapur harian Anda.',
            
            // Kontak
            'contact_email' => 'admin@greenmart.com',
            'contact_phone' => '6281234567890',
            'contact_address' => 'Jl. Raya Panen No. 10, Jakarta Selatan',
            
            // Sosmed
            'link_facebook' => 'https://facebook.com/',
            'link_instagram' => 'https://instagram.com/',
            'link_twitter' => 'https://twitter.com/',
            
            // Gambar (Biarkan null dulu, nanti admin upload sendiri)
            'site_logo' => null,
            'site_favicon' => null,
        ]);
    }
}