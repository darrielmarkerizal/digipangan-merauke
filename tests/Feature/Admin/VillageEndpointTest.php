<?php

use App\Models\User;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_village(string $role = 'super_admin'): User
{
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user;
}

function village_region(string $name = 'Ulilin'): Region
{
    return Region::create(['name' => $name.' '.uniqid()]);
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Village CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.village.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_village('admin'))->getJson(route('api.village.index'))->assertOk();
    });

    it('membuat desa baru', function () {
        $region = village_region();

        $this->actingAs(actor_village())
            ->postJson(route('api.village.store'), ['name' => 'Selil', 'region_id' => $region->id])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Selil');

        $this->assertDatabaseHas('villages', ['name' => 'Selil', 'region_id' => $region->id]);
    });

    it('menolak nama desa duplikat dalam wilayah yang sama dengan 422', function () {
        $region = village_region();
        Village::create(['name' => 'Selil', 'region_id' => $region->id]);

        $this->actingAs(actor_village())
            ->postJson(route('api.village.store'), ['name' => 'Selil', 'region_id' => $region->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('mengizinkan nama desa yang sama di wilayah berbeda', function () {
        $regionA = village_region('Ulilin');
        $regionB = village_region('Muting');
        Village::create(['name' => 'Selil', 'region_id' => $regionA->id]);

        $this->actingAs(actor_village())
            ->postJson(route('api.village.store'), ['name' => 'Selil', 'region_id' => $regionB->id])
            ->assertCreated();
    });

    it('memperbarui desa', function () {
        $region = village_region();
        $village = Village::create(['name' => 'Selil', 'region_id' => $region->id]);

        $this->actingAs(actor_village())
            ->putJson(route('api.village.update', $village->id), ['name' => 'Selil Baru', 'region_id' => $region->id])
            ->assertOk()
            ->assertJsonPath('data.name', 'Selil Baru');
    });

    it('menghapus desa', function () {
        $region = village_region();
        $village = Village::create(['name' => 'Selil', 'region_id' => $region->id]);

        $this->actingAs(actor_village())
            ->deleteJson(route('api.village.destroy', $village->id))
            ->assertOk();

        $this->assertSoftDeleted('villages', ['id' => $village->id]);
    });
});
