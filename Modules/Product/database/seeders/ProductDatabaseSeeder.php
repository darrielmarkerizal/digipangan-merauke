<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;

class ProductDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Sayuran',
            'Buah-buahan',
            'Hasil Perkebunan',
            'Peternakan',
            'Olahan Pangan',
        ];

        foreach ($categories as $index => $name) {
            ProductCategory::firstOrCreate(['name' => $name], ['sort_order' => $index + 1]);
        }

        $units = [
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Ikat', 'symbol' => 'ikat'],
            ['name' => 'Karung', 'symbol' => 'karung'],
            ['name' => 'Ekor', 'symbol' => 'ekor'],
            ['name' => 'Buah', 'symbol' => 'buah'],
            ['name' => 'Liter', 'symbol' => 'l'],
            ['name' => 'Sisir', 'symbol' => 'sisir'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol']], $unit);
        }
    }
}
