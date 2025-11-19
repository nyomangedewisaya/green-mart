<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama
        Notification::truncate();

        $now = Carbon::now();

        // 2. Daftar Notifikasi Broadcast (Sistem)
        $notifications = [
            [
                'target' => 'all',
                'title' => 'Selamat Datang di Green Mart v2.0!',
                'message' => 'Kami telah memperbarui tampilan dan performa sistem. Nikmati pengalaman berbelanja yang lebih cepat.',
                'type' => 'system',
                'created_at' => $now->copy()->subDays(7),
            ],
            [
                'target' => 'sellers',
                'title' => 'Wajib: Lengkapi Data Toko Anda',
                'message' => 'Kepada seluruh Seller, harap segera melengkapi foto profil dan deskripsi toko agar terlihat lebih terpercaya oleh pembeli.',
                'type' => 'system',
                'created_at' => $now->copy()->subDays(3),
            ],
            [
                'target' => 'buyers',
                'title' => 'Promo Gajian Tiba!',
                'message' => 'Dapatkan diskon ongkir spesial untuk pembelian sayur dan buah segar mulai tanggal 25 bulan ini.',
                'type' => 'banner', // Tipe banner/promo
                'created_at' => $now->copy()->subDays(1),
            ],
            [
                'target' => 'all',
                'title' => 'Jadwal Pemeliharaan Sistem',
                'message' => 'Sistem akan mengalami downtime sebentar pada hari Minggu pukul 02:00 - 04:00 WIB untuk peningkatan server.',
                'type' => 'system',
                'created_at' => $now->copy()->subHours(5),
            ],
            [
                'target' => 'sellers',
                'title' => 'Fitur Baru: Kelola Promosi',
                'message' => 'Kabar gembira! Sekarang Anda bisa mengajukan banner promosi untuk toko Anda langsung dari dashboard seller.',
                'type' => 'system',
                'created_at' => $now->copy()->subMinutes(30),
            ],
        ];

        // 3. Masukkan ke Database
        foreach ($notifications as $data) {
            Notification::create([
                'user_id' => null, // NULL karena ini Broadcast Global
                'target' => $data['target'],
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['created_at'],
            ]);
        }
    }
}
