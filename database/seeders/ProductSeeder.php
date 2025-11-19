<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $products = [
            [
                'category_id' => 1, // Makanan & Minuman
                'name' => 'Keripik Pisang Manis 200gr',
                'description' => 'Keripik pisang manis khas Lampung, renyah dan gurih, cocok untuk camilan harian.',
                'price' => 15000,
                'stock' => 120,
                'image' => 'https://placehold.co/300x300/fef08a/78350f?text=Keripik+Pisang',
                'discount' => null,
            ],
            [
                'category_id' => 1,
                'name' => 'Sambal Roa Asli Manado 100gr',
                'description' => 'Sambal roa pedas khas Manado dengan cita rasa ikan roa asap yang menggugah selera.',
                'price' => 25000,
                'stock' => 60,
                'image' => 'https://placehold.co/300x300/f87171/450a0a?text=Sambal+Roa',
                'discount' => 10,
            ],
            [
                'category_id' => 2,
                'name' => 'Tas Rajut Handmade',
                'description' => 'Tas rajut buatan tangan dengan desain unik dan warna pastel, cocok untuk fashion harian.',
                'price' => 85000,
                'stock' => 40,
                'image' => 'https://placehold.co/300x300/fcd34d/78350f?text=Tas+Rajut',
                'discount' => null,
            ],
            [
                'category_id' => 3,
                'name' => 'Kaos Katun Lokal Lengan Pendek',
                'description' => 'Kaos lokal berbahan katun premium dengan sablon tahan lama, nyaman digunakan.',
                'price' => 95000,
                'stock' => 100,
                'image' => 'https://placehold.co/300x300/c7d2fe/1e3a8a?text=Kaos+Katun',
                'discount' => 5,
            ],
            [
                'category_id' => 4,
                'name' => 'Pupuk Organik Cair 1 Liter',
                'description' => 'Pupuk organik ramah lingkungan untuk tanaman sayur dan buah, meningkatkan kesuburan tanah.',
                'price' => 20000,
                'stock' => 75,
                'image' => 'https://placehold.co/300x300/b5f7b7/14532d?text=Pupuk+Organik',
                'discount' => null,
            ],
            [
                'category_id' => 1,
                'name' => 'Kerupuk Udang Tradisional 250gr',
                'description' => 'Kerupuk udang gurih khas Sidoarjo, dibuat dari bahan alami tanpa pengawet.',
                'price' => 18000,
                'stock' => 150,
                'image' => 'https://placehold.co/300x300/fca5a5/7f1d1d?text=Kerupuk+Udang',
                'discount' => 8,
            ],
            [
                'category_id' => 5,
                'name' => 'Sapu Lidi Premium',
                'description' => 'Sapu lidi dari bahan pilihan, kuat, awet, dan ringan digunakan untuk keperluan rumah tangga.',
                'price' => 12000,
                'stock' => 80,
                'image' => 'https://placehold.co/300x300/bae6fd/0c4a6e?text=Sapu+Lidi',
                'discount' => null,
            ],
            [
                'category_id' => 6,
                'name' => 'Kopi Robusta Lampung 250gr',
                'description' => 'Kopi robusta asli Lampung dengan aroma khas dan rasa kuat, cocok untuk pecinta kopi hitam.',
                'price' => 45000,
                'stock' => 90,
                'image' => 'https://placehold.co/300x300/78350f/fef08a?text=Kopi+Robusta',
                'discount' => null,
            ],
            [
                'category_id' => 7,
                'name' => 'Sabun Herbal Lidah Buaya',
                'description' => 'Sabun alami dengan ekstrak lidah buaya untuk melembapkan dan menyehatkan kulit.',
                'price' => 18000,
                'stock' => 200,
                'image' => 'https://placehold.co/300x300/86efac/14532d?text=Sabun+Herbal',
                'discount' => 15,
            ],
            [
                'category_id' => 9,
                'name' => 'Lilin Aromaterapi Vanilla',
                'description' => 'Lilin aromaterapi buatan tangan dengan aroma vanilla lembut, cocok untuk relaksasi.',
                'price' => 35000,
                'stock' => 55,
                'image' => 'https://placehold.co/300x300/f5d0fe/581c87?text=Lilin+Aromaterapi',
                'discount' => null,
            ],
            [
                'category_id' => 8,
                'name' => 'Lampu Meja Bambu',
                'description' => 'Lampu meja minimalis berbahan bambu alami, memberikan kesan hangat dan elegan.',
                'price' => 95000,
                'stock' => 30,
                'image' => 'https://placehold.co/300x300/ddd6fe/312e81?text=Lampu+Bambu',
                'discount' => 5,
            ],
            [
                'category_id' => 2,
                'name' => 'Dompet Anyaman Pandan',
                'description' => 'Dompet anyaman khas Bali dengan motif tradisional dan warna alami.',
                'price' => 30000,
                'stock' => 100,
                'image' => 'https://placehold.co/300x300/fde68a/78350f?text=Dompet+Pandan',
                'discount' => null,
            ],
            [
                'category_id' => 10,
                'name' => 'Kue Kacang Khas Betawi 250gr',
                'description' => 'Kue kacang renyah dan gurih, oleh-oleh khas Betawi yang selalu disukai.',
                'price' => 27000,
                'stock' => 80,
                'image' => 'https://placehold.co/300x300/fca5a5/7f1d1d?text=Kue+Kacang',
                'discount' => null,
            ],
            [
                'category_id' => 5,
                'name' => 'Anyaman Rotan Mini',
                'description' => 'Keranjang kecil dari anyaman rotan, cocok untuk dekorasi rumah atau tempat buah.',
                'price' => 40000,
                'stock' => 60,
                'image' => 'https://placehold.co/300x300/fde68a/78350f?text=Anyaman+Rotan',
                'discount' => null,
            ],
            [
                'category_id' => 9,
                'name' => 'Hiasan Dinding Kayu Ukir',
                'description' => 'Hiasan dinding ukiran tangan bermotif bunga khas Jepara, menambah nilai estetika ruangan.',
                'price' => 150000,
                'stock' => 25,
                'image' => 'https://placehold.co/300x300/f5d0fe/581c87?text=Ukiran+Kayu',
                'discount' => 12,
            ],
        ];

        foreach ($products as $index => $product) {
            DB::table('products')->insert([
                'id' => $index + 1,
                'seller_id' => 1,
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image' => $product['image'],
                'discount' => $product['discount'],
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
