<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin LocalMart',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'avatar' => 'https://ui-avatars.com/api/?name=Admin+LocalMart',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Budi Santoso',
                'email' => 'seller@gmail.com',
                'password' => Hash::make('seller123'),
                'role' => 'seller',
                'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Siti Rahma',
                'email' => 'buyer01@gmail.com',
                'password' => Hash::make('buyer123'),
                'role' => 'buyer',
                'avatar' => 'https://ui-avatars.com/api/?name=Siti+Rahma',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Dewi Putri',
                'email' => 'buyer02@gmail.com',
                'password' => Hash::make('buyer456'),
                'role' => 'buyer',
                'avatar' => 'https://ui-avatars.com/api/?name=Dewi+Putri',
                'status' => 'suspended', // contoh akun dibekukan
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
