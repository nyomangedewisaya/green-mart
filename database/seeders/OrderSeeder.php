<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Ambil data master
        $buyers = User::where('role', 'buyer')->get();
        // Hanya ambil seller yang punya produk (biar tidak error saat ambil produk nanti)
        $sellers = Seller::with('products')->has('products')->get(); 

        if($buyers->isEmpty() || $sellers->isEmpty()) {
            $this->command->info('Data Buyer atau Seller (dengan produk) kosong. Seeder dilewati.');
            return;
        }

        // Simulasi 20 Sesi Belanja (Checkout)
        for ($i = 0; $i < 20; $i++) {
            $buyer = $buyers->random();
            
            // ▼▼▼ PERBAIKAN DI SINI ▼▼▼
            // Hitung jumlah seller yang ada, maksimal ambil 3
            $takeCount = min($sellers->count(), rand(1, 3));
            
            // Ambil seller sejumlah $takeCount
            $selectedSellers = $sellers->random($takeCount);
            // ▲▲▲ ----------------- ▲▲▲

            foreach ($selectedSellers as $seller) {
                // Double check (meski sudah di-filter di query awal)
                if ($seller->products->isEmpty()) continue;

                $status = $faker->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']);
                
                $order = Order::create([
                    'user_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'order_code' => 'INV-' . strtoupper(Str::random(8)),
                    'total_amount' => 0, // Nanti diupdate
                    'status' => $status,
                    'payment_method' => 'Transfer Bank',
                    'address' => $faker->address,
                    'order_date' => $faker->dateTimeBetween('-1 month', 'now'),
                    // Tambahkan receive_date jika completed
                    'receive_date' => $status == 'completed' ? $faker->dateTimeBetween('-1 week', 'now') : null,
                ]);

                // ▼▼▼ PERBAIKAN LOGIKA PRODUK JUGA (UNTUK KEAMANAN) ▼▼▼
                // Ambil maksimal 4 produk, atau sebanyak jumlah produk seller jika kurang dari 4
                $productCountToTake = min($seller->products->count(), rand(1, 4));
                $randomProducts = $seller->products->random($productCountToTake);
                
                $grandTotal = 0;

                foreach ($randomProducts as $product) {
                    $qty = rand(1, 3);
                    $subtotal = $product->price * $qty;
                    
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $product->price,
                        'subtotal' => $subtotal
                    ]);

                    $grandTotal += $subtotal;
                }

                // Update total + ongkir fiktif (15.000)
                $order->update(['total_amount' => $grandTotal + 15000]);
            }
        }
    }
}