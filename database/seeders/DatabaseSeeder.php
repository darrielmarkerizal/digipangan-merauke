<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Farmer\Database\Seeders\FarmerDatabaseSeeder;
use Modules\Page\Database\Seeders\PageDatabaseSeeder;
use Modules\Post\Database\Seeders\PostDatabaseSeeder;
use Modules\Product\Database\Seeders\ProductDatabaseSeeder;
use Modules\Region\Database\Seeders\RegionDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserDatabaseSeeder::class,
            RegionDatabaseSeeder::class,
            FarmerDatabaseSeeder::class,
            ProductDatabaseSeeder::class,
            PostDatabaseSeeder::class,
            PageDatabaseSeeder::class,
        ]);
    }
}
