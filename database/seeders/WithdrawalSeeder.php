<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seller;
use App\Models\Withdrawal;
use Faker\Factory as Faker;

class WithdrawalSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $sellers = Seller::all();

        if($sellers->isEmpty()) return;

        foreach ($sellers as $seller) {
            $seller->update(['balance' => rand(1000000, 10000000)]);

            for ($i = 0; $i < rand(2, 5); $i++) {
                Withdrawal::create([
                    'seller_id' => $seller->id,
                    'amount' => rand(50000, 5000000),
                    'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                    'bank_name' => $faker->randomElement(['BCA', 'BRI', 'Mandiri', 'BNI']),
                    'account_number' => $faker->bankAccountNumber,
                    'account_holder' => $seller->name,
                    'created_at' => \Carbon\Carbon::now()->subDays(rand(0, 7)),
                ]);
            }
        }
    }
}
