<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            'Makanan & Minuman',
            'Kerajinan Tangan',
            'Fashion & Aksesoris',
            'Produk Pertanian',
            'Peralatan Rumah Tangga',
            'Kopi & Minuman Tradisional',
            'Kecantikan & Perawatan',
            'Elektronik Sederhana',
            'Dekorasi Rumah',
            'Oleh-oleh Khas Daerah'
        ];

        foreach ($categories as $index => $category) {
            DB::table('categories')->insert([
                'id' => $index + 1,
                'name' => $category,
                'slug' => Str::slug($category),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
