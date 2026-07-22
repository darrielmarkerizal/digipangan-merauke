<?php

use App\Models\User;
use Modules\Farmer\Models\Farmer;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_unit(string $role = 'super_admin'): User
{
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user;
}

function unit_referencing_product(Unit $unit): Product
{
    $region = Region::create(['name' => 'Ulilin '.uniqid()]);
    $farmer = Farmer::create([
        'region_id' => $region->id,
        'name' => 'Petani '.uniqid(),
        'phone' => '+6281234567890',
    ]);

    return Product::create([
        'product_category_id' => ProductCategory::create(['name' => 'Kategori '.uniqid()])->id,
        'unit_id' => $unit->id,
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

describe('Unit CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.unit.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_unit('admin'))->getJson(route('api.unit.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola master data dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.unit.index'))->assertStatus(403);
    });

    it('membuat satuan baru', function () {
        $this->actingAs(actor_unit())
            ->postJson(route('api.unit.store'), ['name' => 'Kilogram', 'symbol' => 'kg'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Kilogram');

        $this->assertDatabaseHas('units', ['name' => 'Kilogram', 'symbol' => 'kg']);
    });

    it('menolak nama duplikat dengan 422', function () {
        Unit::create(['name' => 'Kilogram', 'symbol' => 'kg']);

        $this->actingAs(actor_unit())
            ->postJson(route('api.unit.store'), ['name' => 'Kilogram', 'symbol' => 'kilo'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('menolak simbol duplikat dengan 422', function () {
        Unit::create(['name' => 'Kilogram', 'symbol' => 'kg']);

        $this->actingAs(actor_unit())
            ->postJson(route('api.unit.store'), ['name' => 'Kiloan', 'symbol' => 'kg'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('symbol');
    });

    it('memperbarui satuan', function () {
        $unit = Unit::create(['name' => 'Ikat', 'symbol' => 'ikat']);

        $this->actingAs(actor_unit())
            ->putJson(route('api.unit.update', $unit->id), ['name' => 'Ikatan', 'symbol' => 'ikat'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Ikatan');
    });

    it('menolak update ke nama satuan lain dengan 422', function () {
        Unit::create(['name' => 'Kilogram', 'symbol' => 'kg']);
        $target = Unit::create(['name' => 'Ikat', 'symbol' => 'ikat']);

        $this->actingAs(actor_unit())
            ->putJson(route('api.unit.update', $target->id), ['name' => 'Kilogram', 'symbol' => 'ikat'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('menghapus satuan yang tidak direferensikan', function () {
        $unit = Unit::create(['name' => 'Karung', 'symbol' => 'karung']);

        $this->actingAs(actor_unit())
            ->deleteJson(route('api.unit.destroy', $unit->id))
            ->assertOk();

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    });

    it('menolak menghapus satuan yang masih dipakai produk dengan 409', function () {
        $unit = Unit::create(['name' => 'Kilogram', 'symbol' => 'kg']);
        unit_referencing_product($unit);

        $this->actingAs(actor_unit())
            ->deleteJson(route('api.unit.destroy', $unit->id))
            ->assertStatus(409);

        $this->assertDatabaseHas('units', ['id' => $unit->id]);
    });
});
