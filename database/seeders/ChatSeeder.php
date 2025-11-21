<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chat;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama (Opsional)
        // Chat::truncate(); 

        $faker = Faker::create('id_ID');

        // 2. Ambil Admin (Penerima/Pengirim Utama)
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->error('Admin user tidak ditemukan. Harap jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // 3. Ambil beberapa Seller dan Buyer secara acak
        $users = User::whereIn('role', ['seller', 'buyer'])->inRandomOrder()->limit(8)->get();

        if ($users->isEmpty()) {
            $this->command->info('Tidak ada seller atau buyer untuk diajak chat.');
            return;
        }

        // 4. Buat Skenario Percakapan
        foreach ($users as $user) {
            
            // Tentukan waktu mulai percakapan (misal: 3 hari lalu)
            $time = Carbon::now()->subDays(rand(0, 5));
            
            // Jumlah pesan dalam satu percakapan (5 - 15 pesan)
            $messageCount = rand(5, 15);

            for ($i = 0; $i < $messageCount; $i++) {
                
                // Ganti-gantian pengirim (User -> Admin -> User -> Admin)
                // Pesan pertama biasanya dari User (bertanya/lapor)
                $isUserSender = ($i % 2 == 0); 
                
                $sender = $isUserSender ? $user : $admin;
                $receiver = $isUserSender ? $admin : $user;

                // Teks pesan dummy yang relevan
                $message = $this->generateMessage($user->role, $isUserSender, $i);

                // Tambahkan waktu (misal balas 5-60 menit kemudian)
                $time->addMinutes(rand(5, 60));

                // Tentukan status read
                // Pesan lama pasti sudah dibaca, pesan terakhir mungkin belum
                $isRead = true;
                if ($i == $messageCount - 1) { // Pesan terakhir
                    $isRead = $faker->boolean(30); // 30% kemungkinan sudah dibaca
                }

                Chat::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'message' => $message,
                    'is_read' => $isRead,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }
    }

    /**
     * Helper sederhana untuk membuat teks chat yang terlihat nyata
     */
    private function generateMessage($role, $isUserSender, $index)
    {
        $faker = Faker::create('id_ID');

        if ($isUserSender) {
            // Pesan dari User (Seller/Buyer) ke Admin
            if ($role == 'seller') {
                $topics = [
                    'Halo Admin, verifikasi toko saya berapa lama ya?',
                    'Min, kenapa produk saya ditolak?',
                    'Saya mau request fitur promosi dong.',
                    'Apakah ada biaya admin bulan ini?',
                    'Terima kasih min, sudah bisa update produk.'
                ];
            } else {
                $topics = [
                    'Min, pesanan saya kok belum dikirim seller?',
                    'Cara refund gimana ya?',
                    'Aplikasi error pas checkout kak.',
                    'Tolong bantu cek resi ini dong.',
                    'Oke makasih infonya min.'
                ];
            }
            // Ambil acak atau generate kalimat faker jika index tinggi
            return ($index < 2) ? $topics[array_rand($topics)] : $faker->sentence(rand(3, 10));

        } else {
            // Pesan dari Admin ke User
            $replies = [
                'Halo, mohon ditunggu ya sedang kami cek.',
                'Bisa kirimkan screenshot kendalanya?',
                'Verifikasi memakan waktu maksimal 1x24 jam hari kerja.',
                'Baik, kendala sudah kami catat.',
                'Sama-sama, senang bisa membantu.',
                'Silakan cek email Anda untuk info lebih lanjut.'
            ];
            return $replies[array_rand($replies)];
        }
    }
}