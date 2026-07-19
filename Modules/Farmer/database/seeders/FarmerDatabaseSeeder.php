<?php

namespace Modules\Farmer\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Farmer\Models\Commodity;

class FarmerDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $commodities = [
            'Padi',
            'Jagung',
            'Rambutan',
            'Ubi Kayu',
            'Kopi',
            'Pinang',
            'Karet',
            'Bawang Merah',
            'Bawang Putih',
            'Cabai Besar',
            'Cabai Keriting',
            'Cabai Rawit',
            'Kentang',
            'Kubis',
            'Tomat',
            'Kacang Panjang',
            'Kangkung',
            'Sawi',
        ];

        foreach ($commodities as $name) {
            Commodity::firstOrCreate(['name' => $name]);
        }
    }
}
