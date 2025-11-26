<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use App\Models\Wishlist;
use Faker\Factory as Faker;

class ReviewWishlistSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $products = Product::all();
        $buyers = User::where('role', 'buyer')->get();

        if($products->isEmpty() || $buyers->isEmpty()) return;

        foreach ($products as $product) {
            
            // 1. Generate Wishlist (Acak 0 - 20 orang per produk)
            $wishlistCount = rand(0, 20);
            $wishlistUsers = $buyers->random(min($buyers->count(), $wishlistCount));
            
            foreach ($wishlistUsers as $user) {
                Wishlist::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $product->id
                ]);
            }

            // 2. Generate Review (Acak 0 - 15 review per produk)
            $reviewCount = rand(0, 15);
            $reviewUsers = $buyers->random(min($buyers->count(), $reviewCount));

            foreach ($reviewUsers as $user) {
                $rating = $faker->numberBetween(1, 5);
                
                // Komen sesuai rating biar realistis
                $comment = match($rating) {
                    5 => $faker->randomElement(['Barang sangat bagus!', 'Pengiriman cepat, mantap.', 'Suka banget, bakal order lagi.', 'Kualitas premium.']),
                    4 => $faker->randomElement(['Barang bagus, tapi pengiriman agak lama.', 'Sesuai harga.', 'Lumayan lah.']),
                    3 => $faker->randomElement(['Biasa saja.', 'Packing kurang rapi.', 'Tidak sesuai ekspektasi tapi oke.']),
                    2 => $faker->randomElement(['Barang agak cacat.', 'Pengiriman sangat lambat.', 'Kecewa.']),
                    1 => $faker->randomElement(['Parah, barang rusak.', 'Penipu, jangan beli disini.', 'Tidak sesuai gambar.']),
                };

                Review::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }
}