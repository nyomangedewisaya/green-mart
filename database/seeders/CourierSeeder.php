<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Courier;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        Courier::truncate();

        $couriers = [
            ['name' => 'JNE', 'slug' => 'jne-reg', 'service' => 'REG', 'cost' => 12000, 'estimation' => '2-3 Hari'],
            ['name' => 'JNE', 'slug' => 'jne-yes', 'service' => 'YES', 'cost' => 24000, 'estimation' => '1 Hari'],
            ['name' => 'J&T', 'slug' => 'jnt-ez', 'service' => 'EZ', 'cost' => 11000, 'estimation' => '2-3 Hari'],
            ['name' => 'SiCepat', 'slug' => 'sicepat-halu', 'service' => 'HALU', 'cost' => 10000, 'estimation' => '3-5 Hari'],
            ['name' => 'GoSend', 'slug' => 'gosend-instant', 'service' => 'Instant', 'cost' => 35000, 'estimation' => 'Jam'],
        ];

        foreach ($couriers as $c) {
            Courier::create($c);
        }
    }
}