<?php

use App\Models\User;
use Modules\Farmer\Models\Commodity;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Page\Database\Seeders\PageDatabaseSeeder;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostCategory;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;

function public_graph(): array
{
    $region = Region::create([
        'name' => 'Ulilin',
        'description' => 'Distrik Ulilin.',
        'agricultural_potential' => 'Padi, jagung, cabai.',
        'area_km2' => 3633.08,
        'population' => 10791,
    ]);
    $village = Village::create(['region_id' => $region->id, 'name' => 'Baidub']);
    $group = FarmerGroup::create(['region_id' => $region->id, 'village_id' => $village->id, 'name' => 'Kelompok Tani Sumber Makmur']);
    $cabai = Commodity::create(['name' => 'Cabai Rawit']);
    $farmer = Farmer::create([
        'region_id' => $region->id,
        'village_id' => $village->id,
        'farmer_group_id' => $group->id,
        'name' => 'Bapak Muhamad Riam',
        'phone' => '+6281234567890',
    ]);
    $farmer->commodities()->sync([$cabai->id]);

    $product = Product::create([
        'product_category_id' => ProductCategory::create(['name' => 'Sayuran'])->id,
        'unit_id' => Unit::create(['name' => 'Kilogram', 'symbol' => 'kg'])->id,
        'farmer_id' => $farmer->id,
        'name' => 'Cabai Rawit Segar',
        'price' => 45000,
        'is_active' => true,
        'is_featured' => true,
        'is_region_featured' => true,
    ]);

    return compact('region', 'farmer', 'product');
}

beforeEach(fn () => $this->withHeader('Origin', config('app.url')));

describe('wilayah publik', function () {
    it('mendaftar wilayah aktif dengan jumlah produk (tanpa auth)', function () {
        public_graph();
        Region::create(['name' => 'Nonaktif', 'is_active' => false]);

        $response = $this->getJson(route('api.public.region.index'))->assertOk();

        expect($response->json('data'))->toHaveCount(1)
            ->and($response->json('data.0.name'))->toBe('Ulilin')
            ->and($response->json('data.0.products_count'))->toBe(1);
    });

    it('menampilkan profil wilayah dengan produk unggulan wilayah', function () {
        ['region' => $region] = public_graph();

        $this->getJson(route('api.public.region.show', $region->slug))
            ->assertOk()
            ->assertJsonPath('data.description', 'Distrik Ulilin.')
            ->assertJsonPath('data.villages_count', 1)
            ->assertJsonPath('data.featured_products.0.name', 'Cabai Rawit Segar');
    });

    it('mengembalikan 404 untuk wilayah nonaktif', function () {
        $region = Region::create(['name' => 'Rahasia', 'is_active' => false]);

        $this->getJson(route('api.public.region.show', $region->slug))->assertStatus(404);
    });

    it('menyediakan sub-resource produk per wilayah', function () {
        ['region' => $region] = public_graph();

        $this->getJson(route('api.public.region.products', $region->slug))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Cabai Rawit Segar')
            ->assertJsonStructure(['data', 'meta', 'links']);
    });
});

describe('petani publik', function () {
    it('menampilkan profil petani beserta produk dan komoditas', function () {
        ['farmer' => $farmer] = public_graph();

        $this->getJson(route('api.public.farmer.show', $farmer->slug))
            ->assertOk()
            ->assertJsonPath('data.name', 'Bapak Muhamad Riam')
            ->assertJsonPath('data.phone', '+6281234567890')
            ->assertJsonPath('data.commodities.0.name', 'Cabai Rawit')
            ->assertJsonPath('data.products.0.name', 'Cabai Rawit Segar');
    });

    it('mengembalikan 404 untuk petani nonaktif', function () {
        $region = Region::create(['name' => 'Ulilin']);
        $farmer = Farmer::create(['region_id' => $region->id, 'name' => 'Sembunyi', 'phone' => '+628100000000', 'is_active' => false]);

        $this->getJson(route('api.public.farmer.show', $farmer->slug))->assertStatus(404);
    });
});

describe('berita publik', function () {
    function published_post(string $title, string $status = Post::STATUS_PUBLISHED): Post
    {
        $author = User::factory()->create(['is_active' => true]);

        return Post::create([
            'post_category_id' => PostCategory::firstOrCreate(['name' => 'Berita Panen'])->id,
            'author_id' => $author->id,
            'title' => $title,
            'body' => 'Isi berita.',
            'status' => $status,
            'published_at' => $status === Post::STATUS_PUBLISHED ? now() : null,
        ]);
    }

    it('hanya menampilkan berita terbit', function () {
        published_post('Terbit');
        published_post('Draf', Post::STATUS_DRAFT);

        $response = $this->getJson(route('api.public.post.index'))->assertOk();

        expect($response->json('data'))->toHaveCount(1)
            ->and($response->json('data.0.title'))->toBe('Terbit');
    });

    it('menampilkan detail berita terbit dan menolak draf dengan 404', function () {
        $published = published_post('Panen Raya');
        $draft = published_post('Belum Terbit', Post::STATUS_DRAFT);

        $this->getJson(route('api.public.post.show', $published->slug))
            ->assertOk()
            ->assertJsonPath('data.title', 'Panen Raya')
            ->assertJsonPath('data.body', 'Isi berita.');

        $this->getJson(route('api.public.post.show', $draft->slug))->assertStatus(404);
    });
});

describe('tentang dan beranda publik', function () {
    it('mengembalikan agregat Tentang dari data ter-seed', function () {
        app(PageDatabaseSeeder::class)->run();

        $this->getJson(route('api.public.about.show'))
            ->assertOk()
            ->assertJsonPath('data.settings.about_background', fn ($v) => filled($v))
            ->assertJsonCount(2, 'data.partners')
            ->assertJsonCount(5, 'data.faqs');
    });

    it('mengembalikan beranda berisi produk unggulan, terbaru, dan kartu wilayah', function () {
        public_graph();

        $this->getJson(route('api.public.home.index'))
            ->assertOk()
            ->assertJsonPath('data.featured_products.0.name', 'Cabai Rawit Segar')
            ->assertJsonPath('data.latest_products.0.name', 'Cabai Rawit Segar')
            ->assertJsonPath('data.regions.0.name', 'Ulilin');
    });
});
