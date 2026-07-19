<?php

namespace Modules\Post\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Post\Models\PostCategory;

class PostDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Berita Panen',
            'Kegiatan Kelompok Tani',
            'Pelatihan',
            'Informasi Harga Pasar',
        ];

        foreach ($categories as $name) {
            PostCategory::firstOrCreate(['name' => $name]);
        }
    }
}
