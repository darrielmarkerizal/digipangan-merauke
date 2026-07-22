<?php

use Modules\Farmer\Models\Farmer;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;

function public_product(array $overrides = [], ?ProductCategory $category = null): Product
{
    $region = Region::create(['name' => 'Ulilin '.uniqid()]);
    $farmer = Farmer::create([
        'region_id' => $region->id,
        'name' => 'Petani '.uniqid(),
        'phone' => '+6281234567890',
    ]);

    return Product::create(array_merge([
        'product_category_id' => ($category ?? ProductCategory::create(['name' => 'Sayuran '.uniqid()]))->id,
        'unit_id' => Unit::create(['name' => 'Kg '.uniqid(), 'symbol' => substr(uniqid(), -8)])->id,
        'farmer_id' => $farmer->id,
        'name' => 'Cabai '.uniqid(),
        'price' => 45000,
        'is_active' => true,
    ], $overrides));
}

beforeEach(fn () => $this->withHeader('Origin', config('app.url')));

describe('katalog produk publik', function () {
    it('dapat diakses tamu tanpa auth dan hanya menampilkan produk aktif', function () {
        public_product(['name' => 'Cabai Aktif']);
        public_product(['name' => 'Cabai Nonaktif', 'is_active' => false]);

        $response = $this->getJson(route('api.public.product.index'))->assertOk();

        expect($response->json('data'))->toHaveCount(1)
            ->and($response->json('data.0.name'))->toBe('Cabai Aktif');
        $response->assertJsonStructure(['data', 'meta' => ['current_page', 'total'], 'links']);
    });

    it('memfilter berdasarkan slug kategori', function () {
        $sayuran = ProductCategory::create(['name' => 'Sayuran']);
        $buah = ProductCategory::create(['name' => 'Buah-buahan']);
        public_product(['name' => 'Kangkung'], $sayuran);
        public_product(['name' => 'Rambutan'], $buah);

        $response = $this->getJson(route('api.public.product.index', ['filter' => ['category' => 'sayuran']]))->assertOk();

        expect($response->json('data'))->toHaveCount(1)
            ->and($response->json('data.0.name'))->toBe('Kangkung');
    });

    it('menampilkan detail produk beserta kontak petani', function () {
        $product = public_product(['name' => 'Cabai Rawit', 'description' => 'Segar']);

        $this->getJson(route('api.public.product.show', $product->slug))
            ->assertOk()
            ->assertJsonPath('data.name', 'Cabai Rawit')
            ->assertJsonPath('data.description', 'Segar')
            ->assertJsonPath('data.farmer.phone', '+6281234567890');
    });

    it('mengembalikan 404 untuk produk nonaktif', function () {
        $product = public_product(['is_active' => false]);

        $this->getJson(route('api.public.product.show', $product->slug))->assertStatus(404);
    });
});
