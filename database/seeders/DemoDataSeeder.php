<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Farmer\Models\Commodity;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Post\Enums\PostStatus;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostCategory;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;

/**
 * Data contoh (bukan data nyata) untuk demo dan serah terima: kelompok tani,
 * petani, produk, dan berita. Nomor telepon adalah contoh dan tidak merujuk
 * orang sungguhan. Seeder ini dipanggil DatabaseSeeder hanya di luar produksi
 * agar lingkungan produksi hanya berisi data referensi yang terverifikasi.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $author = $this->resolveAuthor();

        foreach ($this->farmers() as $data) {
            $this->seedFarmer($data);
        }

        foreach ($this->posts() as $index => $post) {
            $category = PostCategory::where('name', $post['category'])->first();

            if (! $category) {
                continue;
            }

            Post::firstOrCreate(
                ['title' => $post['title']],
                [
                    'post_category_id' => $category->id,
                    'author_id' => $author->id,
                    'body' => $post['body'],
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays(($index + 1) * 3),
                ],
            );
        }
    }

    private function seedFarmer(array $data): void
    {
        $region = Region::where('name', $data['region'])->first();

        if (! $region) {
            return;
        }

        $village = Village::where('region_id', $region->id)->where('name', $data['village'])->first();

        $group = FarmerGroup::firstOrCreate(
            ['region_id' => $region->id, 'name' => $data['group']],
            ['village_id' => $village?->id],
        );

        $farmer = Farmer::firstOrNew(['name' => $data['name']]);
        $farmer->fill([
            'region_id' => $region->id,
            'village_id' => $village?->id,
            'farmer_group_id' => $group->id,
            'phone' => $data['phone'],
            'land_area_ha' => $data['land_area_ha'],
            'is_active' => true,
        ])->save();

        $farmer->commodities()->sync(
            Commodity::whereIn('name', $data['commodities'])->pluck('id')->all()
        );

        foreach ($data['products'] as $item) {
            $this->seedProduct($farmer, $item);
        }
    }

    private function seedProduct(Farmer $farmer, array $item): void
    {
        $category = ProductCategory::where('name', $item['category'])->first();
        $unit = Unit::where('name', $item['unit'])->first();

        if (! $category || ! $unit) {
            return;
        }

        $product = Product::firstOrNew(['name' => $item['name']]);
        $product->fill([
            'product_category_id' => $category->id,
            'unit_id' => $unit->id,
            'farmer_id' => $farmer->id,
            'description' => $item['description'],
            'price' => $item['price'],
            'weight_value' => $item['weight_value'] ?? null,
            'stock_available' => $item['stock_available'] ?? true,
            'is_featured' => $item['is_featured'] ?? false,
            'is_region_featured' => $item['is_region_featured'] ?? false,
            'is_active' => true,
        ])->save(); // region_id terisi otomatis dari petani (lihat Product::booted).
    }

    private function resolveAuthor(): User
    {
        $author = User::first();

        if ($author) {
            return $author;
        }

        $author = User::create([
            'name' => 'Admin Demo',
            'email' => 'demo@digipangan.test',
            'password' => Hash::make(Str::random(24)),
            'is_active' => true,
        ]);
        $author->assignRole('admin');

        return $author;
    }

    private function farmers(): array
    {
        return [
            [
                'name' => 'Bapak Muhamad Riam',
                'region' => 'Elikobel',
                'village' => 'Kweel',
                'group' => 'Kelompok Tani Elikobel',
                'phone' => '+6281200000001',
                'land_area_ha' => 2.5,
                'commodities' => ['Cabai Rawit', 'Tomat', 'Kopi', 'Ubi Kayu'],
                'products' => [
                    [
                        'name' => 'Cabai Rawit Segar Elikobel',
                        'category' => 'Sayuran',
                        'unit' => 'Kilogram',
                        'price' => 45000,
                        'description' => 'Cabai rawit segar hasil panen kelompok tani Distrik Elikobel. Cocok untuk kebutuhan dapur maupun warung makan.',
                        'is_featured' => true,
                        'is_region_featured' => true,
                    ],
                    [
                        'name' => 'Tomat Merah Segar',
                        'category' => 'Sayuran',
                        'unit' => 'Kilogram',
                        'price' => 15000,
                        'description' => 'Tomat merah matang pohon, dipetik saat segar untuk menjaga kualitas.',
                    ],
                    [
                        'name' => 'Kopi Bubuk Elikobel',
                        'category' => 'Hasil Perkebunan',
                        'unit' => 'Kilogram',
                        'price' => 90000,
                        'description' => 'Kopi lokal Elikobel yang diolah petani setempat. Aroma khas dataran Merauke.',
                        'is_region_featured' => true,
                    ],
                ],
            ],
            [
                'name' => 'Ibu Yohana Mahuze',
                'region' => 'Ulilin',
                'village' => 'Baidub',
                'group' => 'Kelompok Tani Sumber Makmur',
                'phone' => '+6281200000002',
                'land_area_ha' => 1.8,
                'commodities' => ['Padi', 'Jagung', 'Kacang Panjang'],
                'products' => [
                    [
                        'name' => 'Beras Lokal Ulilin',
                        'category' => 'Olahan Pangan',
                        'unit' => 'Karung',
                        'price' => 210000,
                        'description' => 'Beras hasil panen sawah Distrik Ulilin, dikemas dalam karung 25 kg.',
                        'weight_value' => 25,
                        'is_featured' => true,
                    ],
                    [
                        'name' => 'Jagung Pipilan Kering',
                        'category' => 'Sayuran',
                        'unit' => 'Kilogram',
                        'price' => 8000,
                        'description' => 'Jagung pipilan kering, cocok untuk pakan maupun olahan pangan.',
                    ],
                ],
            ],
            [
                'name' => 'Bapak Petrus Gebze',
                'region' => 'Muting',
                'village' => 'Waan',
                'group' => 'Kelompok Tani Muting Jaya',
                'phone' => '+6281200000003',
                'land_area_ha' => 3.0,
                'commodities' => ['Ubi Kayu', 'Pinang', 'Rambutan'],
                'products' => [
                    [
                        'name' => 'Rambutan Manis Muting',
                        'category' => 'Buah-buahan',
                        'unit' => 'Kilogram',
                        'price' => 20000,
                        'description' => 'Rambutan manis hasil kebun Distrik Muting, dipanen pada musimnya.',
                        'is_featured' => true,
                        'is_region_featured' => true,
                    ],
                    [
                        'name' => 'Ubi Kayu Segar',
                        'category' => 'Sayuran',
                        'unit' => 'Kilogram',
                        'price' => 7000,
                        'description' => 'Ubi kayu segar, bahan pangan pokok yang banyak dibudidayakan warga transmigrasi.',
                    ],
                ],
            ],
            [
                'name' => 'Ibu Maria Basik-Basik',
                'region' => 'Ulilin',
                'village' => 'Kireli',
                'group' => 'Kelompok Tani Sumber Makmur',
                'phone' => '+6281200000004',
                'land_area_ha' => 1.2,
                'commodities' => ['Kubis', 'Kangkung', 'Sawi'],
                'products' => [
                    [
                        'name' => 'Kangkung Segar Ikat',
                        'category' => 'Sayuran',
                        'unit' => 'Ikat',
                        'price' => 5000,
                        'description' => 'Kangkung segar dipetik pagi hari, dijual per ikat.',
                        'is_region_featured' => true,
                    ],
                    [
                        'name' => 'Kubis Segar',
                        'category' => 'Sayuran',
                        'unit' => 'Kilogram',
                        'price' => 12000,
                        'description' => 'Kubis padat dan segar dari kebun hortikultura Distrik Ulilin.',
                    ],
                ],
            ],
        ];
    }

    private function posts(): array
    {
        return [
            [
                'title' => 'Panen Raya Cabai Rawit di Distrik Elikobel',
                'category' => 'Berita Panen',
                'body' => 'Kelompok tani di Distrik Elikobel menuai hasil panen cabai rawit yang melimpah pada musim ini. Panen bersama menjadi momentum petani memasarkan komoditas melalui etalase DigiPangan agar menjangkau lebih banyak pembeli.',
            ],
            [
                'title' => 'Pelatihan Pemasaran Digital untuk Kelompok Tani',
                'category' => 'Pelatihan',
                'body' => 'Tim pendamping menggelar pelatihan pemasaran digital bagi kelompok tani kawasan transmigrasi. Materi mencakup pengelolaan profil produk, penetapan harga acuan, dan cara menanggapi calon pembeli melalui tombol Hubungi Penjual.',
            ],
            [
                'title' => 'Gotong Royong Kelompok Tani Sumber Makmur',
                'category' => 'Kegiatan Kelompok Tani',
                'body' => 'Anggota Kelompok Tani Sumber Makmur di Distrik Ulilin bergotong royong menyiapkan lahan tanam untuk musim berikutnya. Kegiatan ini memperkuat kelembagaan petani sekaligus menjaga keberlanjutan produksi.',
            ],
            [
                'title' => 'Informasi Harga Komoditas Pekan Ini',
                'category' => 'Informasi Harga Pasar',
                'body' => 'Berikut gambaran harga acuan sejumlah komoditas dari petani kawasan transmigrasi pekan ini. Harga dapat berubah mengikuti musim dan ketersediaan; konfirmasi akhir dilakukan langsung dengan penjual.',
            ],
        ];
    }
}
