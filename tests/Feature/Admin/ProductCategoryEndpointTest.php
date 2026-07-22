<?php

use App\Models\User;
use Modules\Farmer\Models\Farmer;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_product_category(string $role = 'super_admin'): User
{
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user;
}

function product_category_referencing_product(ProductCategory $category): Product
{
    $region = Region::create(['name' => 'Ulilin '.uniqid()]);
    $farmer = Farmer::create([
        'region_id' => $region->id,
        'name' => 'Petani '.uniqid(),
        'phone' => '+6281234567890',
    ]);

    return Product::create([
        'product_category_id' => $category->id,
        'unit_id' => Unit::create(['name' => 'Satuan '.uniqid(), 'symbol' => substr(uniqid(), -8)])->id,
        'farmer_id' => $farmer->id,
        'region_id' => $region->id,
        'name' => 'Produk '.uniqid(),
        'price' => 10000,
    ]);
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('ProductCategory CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.product_category.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_product_category('admin'))->getJson(route('api.product_category.index'))->assertOk();
    });

    it('membuat kategori baru', function () {
        $this->actingAs(actor_product_category())
            ->postJson(route('api.product_category.store'), ['name' => 'Sayuran', 'sort_order' => 1])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Sayuran');

        $this->assertDatabaseHas('product_categories', ['name' => 'Sayuran']);
    });

    it('menolak nama duplikat dengan 422', function () {
        ProductCategory::create(['name' => 'Sayuran']);

        $this->actingAs(actor_product_category())
            ->postJson(route('api.product_category.store'), ['name' => 'Sayuran'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('memperbarui kategori', function () {
        $category = ProductCategory::create(['name' => 'Buah']);

        $this->actingAs(actor_product_category())
            ->putJson(route('api.product_category.update', $category->id), ['name' => 'Buah-buahan'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Buah-buahan');
    });

    it('menghapus kategori yang tidak direferensikan', function () {
        $category = ProductCategory::create(['name' => 'Olahan Pangan']);

        $this->actingAs(actor_product_category())
            ->deleteJson(route('api.product_category.destroy', $category->id))
            ->assertOk();

        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);
    });

    it('menolak menghapus kategori yang masih dipakai produk dengan 409', function () {
        $category = ProductCategory::create(['name' => 'Sayuran']);
        product_category_referencing_product($category);

        $this->actingAs(actor_product_category())
            ->deleteJson(route('api.product_category.destroy', $category->id))
            ->assertStatus(409);

        $this->assertDatabaseHas('product_categories', ['id' => $category->id]);
    });
});
