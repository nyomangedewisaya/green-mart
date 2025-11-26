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
        // Hapus data lama (Opsional, aktifkan jika ingin reset bersih)
        // Order::truncate();
        // OrderDetail::truncate();

        $faker = Faker::create('id_ID');
        
        // 1. Ambil Buyer
        $buyers = User::where('role', 'buyer')->get();
        // 2. Ambil Seller yang punya produk
        $sellers = Seller::with('products')->has('products')->get();

        if($buyers->isEmpty() || $sellers->isEmpty()) {
            $this->command->info('Data Buyer atau Seller kosong. Seeder dilewati.');
            return;
        }

        // Buat 30 Transaksi Dummy
        for ($i = 0; $i < 30; $i++) {
            $buyer = $buyers->random();
            $seller = $sellers->random();

            // Status Acak
            $status = $faker->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']);
            
            // Data Pengiriman (Simulasi Manual/Flat Rate)
            $shippingCost = 15000; // Ongkir Flat
            $courier = 'JNE';      // Kurir Default
            $service = 'REG';
            $resi = null;
            $receiveDate = null;

            if (in_array($status, ['shipped', 'completed'])) {
                $resi = 'JP' . strtoupper(Str::random(10)); 
            }
            if ($status === 'completed') {
                $receiveDate = $faker->dateTimeBetween('-1 week', 'now');
            }

            // 1. Buat Order Header
            $order = Order::create([
                'user_id' => $buyer->id,
                'seller_id' => $seller->id,
                'order_code' => 'INV-' . strtoupper(Str::random(9)),
                'total_amount' => 0, // Nanti diupdate
                'status' => $status,
                'payment_method' => 'Transfer Bank',
                'address' => $faker->address,
                
                // Kolom Pengiriman (Sesuai migrasi tambahan sebelumnya)
                'shipping_cost' => $shippingCost,
                'shipping_courier' => $courier,
                'shipping_service' => $service,
                'shipping_resi' => $resi,
                
                'order_date' => $faker->dateTimeBetween('-2 months', 'now'),
                'receive_date' => $receiveDate,
            ]);

            // 2. Isi Detail Produk (Sesuai request: price & subtotal ada)
            $subtotalProduk = 0;
            
            // Ambil 1-3 produk acak dari seller ini
            $productCountToTake = min($seller->products->count(), rand(1, 3));
            $randomProducts = $seller->products->random($productCountToTake);

            foreach ($randomProducts as $product) {
                $qty = rand(1, 5); // Beli 1-5 pcs
                $price = $product->price; // Harga satuan saat beli
                $lineTotal = $price * $qty; // Subtotal per item

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,       // <--- Sesuai request Anda
                    'subtotal' => $lineTotal // <--- Sesuai request Anda
                ]);

                $subtotalProduk += $lineTotal;
            }

            // 3. Update Total Belanja (Produk + Ongkir)
            $finalTotal = $subtotalProduk + $shippingCost;
            $order->update(['total_amount' => $finalTotal]);
        }
    }
}