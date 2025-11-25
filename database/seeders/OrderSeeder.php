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
        // 1. Bersihkan data lama agar bersih (Opsional, matikan jika tidak ingin dihapus)
        // Order::truncate();
        // OrderDetail::truncate();

        $faker = Faker::create('id_ID');
        
        // Ambil Buyer dan Seller yang punya produk
        $buyers = User::where('role', 'buyer')->get();
        $sellers = Seller::with('products')->has('products')->get();

        if($buyers->isEmpty() || $sellers->isEmpty()) {
            $this->command->info('Data Buyer atau Seller kosong. Seeder dilewati.');
            return;
        }

        // Buat 30 Transaksi Dummy
        for ($i = 0; $i < 30; $i++) {
            $buyer = $buyers->random();
            
            // Satu buyer bisa beli di 1-2 toko sekaligus (Split Order Logic)
            $shopCount = rand(1, 2);
            // Pastikan tidak mengambil lebih dari jumlah seller yang ada
            $shopCount = min($sellers->count(), $shopCount);
            
            $selectedSellers = $sellers->random($shopCount);

            foreach ($selectedSellers as $seller) {
                // Status Acak
                $status = $faker->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']);
                
                // Tentukan Data Pengiriman (Hanya jika sudah dikirim/selesai)
                $courier = null;
                $resi = null;
                $receiveDate = null;

                if (in_array($status, ['shipped', 'completed'])) {
                    $courier = $faker->randomElement(['JNE', 'J&T', 'SiCepat', 'GoSend']);
                    $resi = 'JP' . strtoupper(Str::random(10)); // Contoh resi
                }

                if ($status === 'completed') {
                    $receiveDate = $faker->dateTimeBetween('-1 week', 'now');
                }

                // Buat Order Header
                $order = Order::create([
                    'user_id' => $buyer->id,
                    'seller_id' => $seller->id, // Relasi langsung ke Seller
                    'order_code' => 'INV-' . strtoupper(Str::random(9)), // INV-X7A9B2C1
                    'total_amount' => 0, // Hitung nanti
                    'status' => $status,
                    'payment_method' => 'Transfer Bank',
                    'address' => $faker->address,
                    'shipping_courier' => $courier,
                    'shipping_resi' => $resi,
                    'order_date' => $faker->dateTimeBetween('-2 months', 'now'),
                    'receive_date' => $receiveDate,
                ]);

                // Isi Produk (Detail Order)
                $grandTotal = 0;
                
                // Ambil 1-3 produk acak dari toko ini
                $productCountToTake = min($seller->products->count(), rand(1, 3));
                $randomProducts = $seller->products->random($productCountToTake);

                foreach ($randomProducts as $product) {
                    $qty = rand(1, 5); // Beli 1-5 pcs
                    $price = $product->price; // Harga saat beli (snapshot)
                    $subtotal = $price * $qty;

                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal
                    ]);

                    $grandTotal += $subtotal;
                }

                // Update Total Belanja (+ Ongkir Fiktif 15rb)
                $finalTotal = $grandTotal + 15000;
                
                // Jika statusnya cancelled atau pending, anggap belum bayar/batal bayar
                // Tapi total_amount tetap dicatat sebagai nilai order
                $order->update(['total_amount' => $finalTotal]);
            }
        }
    }
}