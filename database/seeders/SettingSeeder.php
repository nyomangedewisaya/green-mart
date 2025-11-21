<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tabel kosong sebelum isi baru
        Setting::truncate();

        Setting::create([
            // Visual
            'site_name' => 'Green Mart',
            'site_tagline' => 'Segar, Hemat, dan Terpercaya',
            'site_description' => 'Green Mart adalah platform marketplace terdepan untuk jual beli sayur, buah, dan kebutuhan dapur harian langsung dari petani lokal.',
            
            // Kontak
            'contact_email' => 'support@greenmart.id',
            'contact_phone' => '6281234567890', // Format 62 lebih aman
            'contact_address' => 'Jl. Teknologi No. 10, Kawasan Digital, Jakarta Selatan, 12345',
            
            // Sosmed (Kosongkan atau isi dummy)
            'link_instagram' => 'https://instagram.com/greenmart.id',
            'link_facebook'  => 'https://facebook.com/greenmart',
        ]);
    }
}