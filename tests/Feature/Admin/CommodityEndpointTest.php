<?php

use App\Models\User;
use Modules\Farmer\Models\Commodity;
use Modules\Farmer\Models\Farmer;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_commodity(string $role = 'super_admin'): User
{
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user;
}

function commodity_attached_to_farmer(Commodity $commodity): Farmer
{
    $region = Region::create(['name' => 'Ulilin '.uniqid()]);
    $farmer = Farmer::create([
        'region_id' => $region->id,
        'name' => 'Petani '.uniqid(),
        'phone' => '+6281234567890',
    ]);
    $farmer->commodities()->attach($commodity->id);

    return $farmer;
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Commodity CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.commodity.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_commodity('admin'))->getJson(route('api.commodity.index'))->assertOk();
    });

    it('membuat komoditas baru', function () {
        $this->actingAs(actor_commodity())
            ->postJson(route('api.commodity.store'), ['name' => 'Cabai Rawit'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Cabai Rawit');

        $this->assertDatabaseHas('commodities', ['name' => 'Cabai Rawit']);
    });

    it('menolak nama duplikat dengan 422', function () {
        Commodity::create(['name' => 'Cabai Rawit']);

        $this->actingAs(actor_commodity())
            ->postJson(route('api.commodity.store'), ['name' => 'Cabai Rawit'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('memperbarui komoditas', function () {
        $commodity = Commodity::create(['name' => 'Kopi']);

        $this->actingAs(actor_commodity())
            ->putJson(route('api.commodity.update', $commodity->id), ['name' => 'Kopi Arabika'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Kopi Arabika');
    });

    it('menghapus komoditas yang tidak direferensikan', function () {
        $commodity = Commodity::create(['name' => 'Pinang']);

        $this->actingAs(actor_commodity())
            ->deleteJson(route('api.commodity.destroy', $commodity->id))
            ->assertOk();

        $this->assertDatabaseMissing('commodities', ['id' => $commodity->id]);
    });

    it('menolak menghapus komoditas yang masih dipakai petani dengan 409', function () {
        $commodity = Commodity::create(['name' => 'Cabai Rawit']);
        commodity_attached_to_farmer($commodity);

        $this->actingAs(actor_commodity())
            ->deleteJson(route('api.commodity.destroy', $commodity->id))
            ->assertStatus(409);

        $this->assertDatabaseHas('commodities', ['id' => $commodity->id]);
    });
});
