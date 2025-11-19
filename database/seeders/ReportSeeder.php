<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Report;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();
        $sellerIds = Seller::pluck('id')->toArray();

        if (empty($userIds) || (empty($productIds) && empty($sellerIds))) {
            $this->command->error('Data User, Product, atau Seller kosong. Harap seed data master terlebih dahulu.');
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            $target = $faker->randomElement(['product', 'seller']);
            $productId = null;
            $sellerId = null;

            if ($target === 'product' && !empty($productIds)) {
                $productId = $faker->randomElement($productIds);
            } elseif (!empty($sellerIds)) {
                $sellerId = $faker->randomElement($sellerIds);
            } else {
                continue;
            }

            Report::create([
                'user_id' => $faker->randomElement($userIds),
                'product_id' => $productId,
                'seller_id' => $sellerId,
                'reason' => $faker->randomElement(['fake', 'mismatch', 'scam', 'others']),
                'description' => $faker->paragraph(2), 
                'status' => $faker->randomElement(['pending', 'pending', 'resolved', 'rejected']),
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                'updated_at' => now(),
            ]);
        }
    }
}
