<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. MATIKAN Foreign Key Checks (Agar bisa truncate)
        Schema::disableForeignKeyConstraints();

        // 2. KOSONGKAN TABEL (Urutan: Pivot dulu, baru Parent)
        DB::table('notification_user')->truncate(); // Hapus data interaksi user
        Notification::truncate();                   // Hapus data notifikasi utama

        // 3. NYALAKAN KEMBALI Foreign Key Checks
        Schema::enableForeignKeyConstraints();

        // --- MULAI SEEDING DATA ---

        // A. Buat Notifikasi Global (Target: All)
        $promo = Notification::create([
            'title'   => 'Promo Gajian Hemat! 🤑',
            'message' => 'Dapatkan diskon hingga 50% untuk semua produk sayuran segar hanya hari ini.',
            'type'    => 'info', // info/success/warning/danger
            'target'  => 'all',
        ]);

        // B. Buat Notifikasi Khusus Seller
        $maintenance = Notification::create([
            'title'   => 'Jadwal Maintenance Sistem ⚠️',
            'message' => 'Sistem akan mengalami pemeliharaan pada jam 02:00 - 04:00 WIB. Mohon selesaikan pesanan sebelum jam tersebut.',
            'type'    => 'warning',
            'target'  => 'sellers',
        ]);

        // C. Buat Notifikasi Personal (Contoh: untuk Seller Pertama)
        $firstSeller = User::where('role', 'seller')->first();
        if ($firstSeller) {
            Notification::create([
                'user_id' => $firstSeller->id,
                'title'   => 'Selamat Datang, Seller! 🎉',
                'message' => 'Terima kasih telah bergabung. Segera lengkapi profil toko Anda untuk mulai berjualan.',
                'type'    => 'success',
                'target'  => 'personal',
            ]);

            // Simulasi: Seller ini sudah membaca notifikasi Promo (Masuk tabel pivot)
            $promo->users()->attach($firstSeller->id, [
                'read_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}