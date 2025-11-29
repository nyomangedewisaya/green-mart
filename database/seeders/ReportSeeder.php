<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\User;
use App\Models\Product;
use App\Models\Seller;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reporters = User::where('role', 'buyer')->take(3)->get();
        
        if ($reporters->isEmpty()) return;

        $product = Product::first();
        if ($product) {
            Report::create([
                'user_id'     => $reporters->random()->id,
                'target_id'   => $product->id,
                'target_type' => Product::class, 
                'reason'      => 'Barang Palsu / KW',
                'description' => 'Saya curiga produk ini tidak original karena harganya terlalu murah dan kemasan berbeda.',
                'status'      => 'pending',
            ]);
        }

        $seller = Seller::first();
        if ($seller) {
            Report::create([
                'user_id'     => $reporters->random()->id,
                'target_id'   => $seller->id,
                'target_type' => Seller::class,
                'reason'      => 'Toko Fiktif',
                'description' => 'Toko ini meminta transfer di luar aplikasi dan chat kasar.',
                'status'      => 'pending',
            ]);
        }

        $badBuyer = User::where('role', 'buyer')->whereNotIn('id', $reporters->pluck('id'))->first();
        
        $sellerUser = User::where('role', 'seller')->first();
        
        if ($badBuyer && $sellerUser) {
            Report::create([
                'user_id'     => $sellerUser->id, 
                'target_id'   => $badBuyer->id,   
                'target_type' => User::class,     
                'reason'      => 'Menolak Bayar COD',
                'description' => 'Paket sudah sampai tapi pembeli menolak membayar dan memaki kurir.',
                'status'      => 'pending',
            ]);
        }

        if ($product) {
            Report::create([
                'user_id'     => $reporters->random()->id,
                'target_id'   => $product->id,
                'target_type' => Product::class,
                'reason'      => 'Produk Terlaris',
                'description' => 'Salah kategori produk.',
                'status'      => 'resolved',
                'admin_note'  => 'Kategori produk sudah kami perbaiki.',
                'created_at'  => now()->subDays(3),
            ]);
        }
    }
}