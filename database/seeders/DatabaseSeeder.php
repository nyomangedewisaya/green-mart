<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SellerSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            PromotionSeeder::class,
            ReportSeeder::class,
            NotificationSeeder::class,
            OrderSeeder::class,
            ChatSeeder::class,
            WithdrawalSeeder::class,
        ]);

        // php artisan db:seed --class=NamaSeeder
    }
}
