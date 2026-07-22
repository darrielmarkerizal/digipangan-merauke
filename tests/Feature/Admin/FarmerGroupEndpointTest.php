<?php

use App\Models\User;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_farmer_group(string $role = 'super_admin'): User
{
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user;
}

function farmer_group_region(string $name = 'Elikobel'): Region
{
    return Region::create(['name' => $name.' '.uniqid()]);
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('FarmerGroup CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.farmer_group.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_farmer_group('admin'))->getJson(route('api.farmer_group.index'))->assertOk();
    });

    it('membuat kelompok tani baru', function () {
        $region = farmer_group_region();

        $this->actingAs(actor_farmer_group())
            ->postJson(route('api.farmer_group.store'), ['name' => 'Kelompok Tani Elikobel', 'region_id' => $region->id])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Kelompok Tani Elikobel');

        $this->assertDatabaseHas('farmer_groups', ['name' => 'Kelompok Tani Elikobel', 'region_id' => $region->id]);
    });

    it('menolak region_id yang tidak ada dengan 422', function () {
        $this->actingAs(actor_farmer_group())
            ->postJson(route('api.farmer_group.store'), ['name' => 'Kelompok Tani Maju', 'region_id' => 999999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('region_id');
    });

    it('menolak nama duplikat dengan 422', function () {
        $region = farmer_group_region();
        FarmerGroup::create(['name' => 'Kelompok Tani Elikobel', 'region_id' => $region->id]);

        $this->actingAs(actor_farmer_group())
            ->postJson(route('api.farmer_group.store'), ['name' => 'Kelompok Tani Elikobel', 'region_id' => $region->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('memperbarui kelompok tani', function () {
        $region = farmer_group_region();
        $group = FarmerGroup::create(['name' => 'Kelompok Tani Lama', 'region_id' => $region->id]);

        $this->actingAs(actor_farmer_group())
            ->putJson(route('api.farmer_group.update', $group->id), ['name' => 'Kelompok Tani Baru', 'region_id' => $region->id])
            ->assertOk()
            ->assertJsonPath('data.name', 'Kelompok Tani Baru');
    });

    it('menghapus kelompok tani', function () {
        $region = farmer_group_region();
        $group = FarmerGroup::create(['name' => 'Kelompok Tani Hapus', 'region_id' => $region->id]);

        $this->actingAs(actor_farmer_group())
            ->deleteJson(route('api.farmer_group.destroy', $group->id))
            ->assertOk();

        $this->assertSoftDeleted('farmer_groups', ['id' => $group->id]);
    });
});
