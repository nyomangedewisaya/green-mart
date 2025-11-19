<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        Promotion::query()->delete();

        $sellers = DB::table('sellers')->pluck('id');

        if ($sellers->isEmpty()) {
            $this->command->error('Tidak ada data di tabel "sellers". Mohon jalankan Seeder untuk tabel "sellers" terlebih dahulu.');
            return;
        }

        $now = Carbon::now();
        $promotions = [
            [
                'title' => 'Diskon Kilat 50% Buah Segar Pilihan',
                'link' => 'https://greenmart.test/kategori/buah',
                'price' => 150000,
                'start_date' => $now->copy()->subDays(5),
                'end_date' => $now->copy()->addDays(10),
                'status' => 'paid',
                'is_active' => true,
                'image' => 'https://placehold.co/800x400/a7f3d0/052e16?text=Diskon+Buah+Segar'
            ],
            [
                'title' => 'Sayuran Organik Gratis Ongkir',
                'link' => 'https://greenmart.test/kategori/sayuran',
                'price' => 100000,
                'start_date' => $now->copy()->subDays(20),
                'end_date' => $now->copy()->subDays(5),
                'status' => 'expired',
                'is_active' => false,
                'image' => 'https://placehold.co/800x400/bbf7d0/14532d?text=Gratis+Ongkir'
            ],
            [
                'title' => 'Promo Daging Sapi Premium (Pending)',
                'link' => 'https://greenmart.test/produk/daging-wagyu-a5',
                'price' => 200000,
                'start_date' => $now->copy()->addDays(2),
                'end_date' => $now->copy()->addDays(9),
                'status' => 'pending', 
                'is_active' => false,
                'image' => 'https://placehold.co/800x400/fecaca/7f1d1d?text=Daging+Premium'
            ],
            [
                'title' => 'Beli 1 Gratis 1 Minuman Sehat',
                'link' => 'https://greenmart.test/kategori/minuman',
                'price' => 120000,
                'start_date' => $now->copy()->addDays(1),
                'end_date' => $now->copy()->addDays(8),
                'status' => 'paid',
                'is_active' => true, 
                'image' => 'https://placehold.co/800x400/fef08a/78350f?text=Beli+1+Gratis+1'
            ],
            [
                'title' => 'Pesta Seafood Akhir Pekan (Expired)',
                'link' => 'https://greenmart.test/kategori/seafood',
                'price' => 180000,
                'start_date' => $now->copy()->subDays(15),
                'end_date' => $now->copy()->subDays(10),
                'status' => 'expired',
                'is_active' => false,
                'image' => 'https://placehold.co/800x400/bae6fd/0c4a6e?text=Pesta+Seafood'
            ],
            [
                'title' => 'Jajanan Pasar Diskon 20% (Aktif)',
                'link' => 'https://greenmart.test/kategori/jajanan-pasar',
                'price' => 75000,
                'start_date' => $now->copy()->subDays(2),
                'end_date' => $now->copy()->addDays(5),
                'status' => 'paid',
                'is_active' => true,
                'image' => 'https://placehold.co/800x400/f5d0fe/581c87?text=Jajanan+Pasar'
            ],
            [
                'title' => 'Kebutuhan Dapur Hemat (Pending)',
                'link' => 'https://greenmart.test/kategori/bumbu',
                'price' => 100000,
                'start_date' => $now->copy()->addDays(5),
                'end_date' => $now->copy()->addDays(15),
                'status' => 'pending',
                'is_active' => false,
                'image' => 'https://placehold.co/800x400/d1d5db/1f2937?text=Kebutuhan+Dapur'
            ],
            [
                'title' => 'Promo Susu Impor Murah (Non-Aktif)',
                'link' => 'https://greenmart.test/kategori/susu',
                'price' => 130000,
                'start_date' => $now->copy()->subDays(1),
                'end_date' => $now->copy()->addDays(6),
                'status' => 'paid',
                'is_active' => false, 
                'image' => 'https://placehold.co/800x400/f0f9ff/075985?text=Susu+Impor'
            ],
            [
                'title' => 'Cemilan Sehat Anak (Expired)',
                'link' => 'https://greenmart.test/kategori/cemilan',
                'price' => 90000,
                'start_date' => $now->copy()->subDays(30),
                'end_date' => $now->copy()->subDays(20),
                'status' => 'expired',
                'is_active' => false,
                'image' => 'https://placehold.co/800x400/fef3c7/713f12?text=Cemilan+Anak'
            ],
            [
                'title' => 'Perlengkapan Bayi Diskon Besar (Aktif)',
                'link' => 'https://greenmart.test/kategori/bayi',
                'price' => 250000,
                'start_date' => $now->copy()->subDays(10),
                'end_date' => $now->copy()->addDays(20),
                'status' => 'paid',
                'is_active' => true,
                'image' => 'https://placehold.co/800x400/e0e7ff/3730a3?text=Perlengkapan+Bayi'
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::create([
                'seller_id' => $sellers->random(),
                'title' => $promo['title'],
                'slug' => Str::slug($promo['title']) . '-' . uniqid(),
                'image' => $promo['image'],
                'link' => $promo['link'],
                'price' => $promo['price'],
                'start_date' => $promo['start_date'],
                'end_date' => $promo['end_date'],
                'status' => $promo['status'],
                'is_active' => $promo['is_active'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
