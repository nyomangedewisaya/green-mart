<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('sellers')->insert([
            [
                'id' => 1,
                'user_id' => 2, 
                'name' => 'Warung Budi Jaya',
                'slug' => Str::slug('Warung Budi Jaya'),
                'description' => 'Menjual berbagai kebutuhan harian dan sembako.',
                'address' => 'Jl. Melati No. 15, Surabaya',
                'phone' => '081234567890',
                'logo' => 'uploads/sellers/warung-budi-logo.png',
                'banner' => 'uploads/sellers/warung-budi-banner.jpg',
                'is_verified' => true, 
                'rating' => 4.50,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
