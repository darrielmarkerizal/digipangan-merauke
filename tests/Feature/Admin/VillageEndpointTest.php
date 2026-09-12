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

    it('mengizinkan Super Admin mengakses daftar', function () {
        $this->actingAs(actor_village('super_admin'))->getJson(route('api.village.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola master data dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.village.index'))->assertStatus(403);
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

describe('Village Admin Web CRUD', function () {
    it('menampilkan halaman tambah desa untuk admin distrik yang memiliki penugasan', function () {
        $region = village_region();
        $admin = actor_village('admin_distrik');
        $admin->update(['region_id' => $region->id]);

        $this->actingAs($admin)
            ->get(route('admin.village.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Village/Create')
                ->where('default_region_id', $region->id)
                ->has('regions', 1)
            );
    });

    it('mengizinkan admin distrik membuat desa pada wilayahnya', function () {
        $region = village_region();
        $admin = actor_village('admin_distrik');
        $admin->update(['region_id' => $region->id]);

        $this->actingAs($admin)
            ->post(route('admin.village.store'), [
                'name' => 'Kampung Pemekaran',
                'region_id' => $region->id,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.village.index'));

        $this->assertDatabaseHas('villages', [
            'name' => 'Kampung Pemekaran',
            'region_id' => $region->id,
            'is_active' => true,
        ]);
    });

    it('mengabaikan wilayah dari request dan memakai wilayah penugasan admin distrik', function () {
        $assignedRegion = village_region('Assigned');
        $otherRegion = village_region('Other');
        $admin = actor_village('admin_distrik');
        $admin->update(['region_id' => $assignedRegion->id]);

        $this->actingAs($admin)
            ->post(route('admin.village.store'), [
                'name' => 'Desa Tidak Sah',
                'region_id' => $otherRegion->id,
            ])
            ->assertRedirect(route('admin.village.index'));

        $this->assertDatabaseHas('villages', [
            'name' => 'Desa Tidak Sah',
            'region_id' => $assignedRegion->id,
        ]);
    });

    it('mengembalikan validasi 422 untuk nama desa duplikat pada web admin', function () {
        $region = village_region();
        Village::create(['name' => 'Selil', 'region_id' => $region->id]);
        $admin = actor_village('admin_distrik');
        $admin->update(['region_id' => $region->id]);

        $this->actingAs($admin)
            ->from(route('admin.village.create'))
            ->post(route('admin.village.store'), [
                'name' => 'Selil',
                'region_id' => $region->id,
            ])
            ->assertRedirect(route('admin.village.create'))
            ->assertSessionHasErrors('name');
    });
});
