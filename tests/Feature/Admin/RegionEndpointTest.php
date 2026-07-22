<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Media\Services\TemporaryMediaService;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_region(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Region CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.region.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_region('admin'))->getJson(route('api.region.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola wilayah dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.region.index'))->assertStatus(403);
    });

    it('membuat wilayah baru', function () {
        $this->actingAs(actor_region())
            ->postJson(route('api.region.store'), [
                'name' => 'Ulilin',
                'description' => 'Distrik Ulilin',
                'area_km2' => 3633.08,
                'population' => 10791,
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Ulilin');

        $this->assertDatabaseHas('regions', ['name' => 'Ulilin', 'population' => 10791]);
    });

    it('menghitung desa dan kelompok tani via withCount', function () {
        $region = Region::create(['name' => 'Ulilin '.uniqid()]);
        $village = Village::create(['region_id' => $region->id, 'name' => 'Selil '.uniqid()]);
        FarmerGroup::create(['region_id' => $region->id, 'village_id' => $village->id, 'name' => 'Kelompok '.uniqid()]);

        $this->actingAs(actor_region())
            ->getJson(route('api.region.show', $region->id))
            ->assertOk()
            ->assertJsonPath('data.villages_count', 1)
            ->assertJsonPath('data.farmer_groups_count', 1);
    });

    it('memperbarui wilayah', function () {
        $region = Region::create(['name' => 'Muting '.uniqid()]);

        $this->actingAs(actor_region())
            ->putJson(route('api.region.update', $region->id), [
                'name' => 'Muting Baru',
                'population' => 5000,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Muting Baru')
            ->assertJsonPath('data.population', 5000);
    });

    it('melampirkan cover dan galeri dari unggahan sementara', function () {
        Storage::fake('local');
        Storage::fake('public');

        $service = app(TemporaryMediaService::class);
        $cover = $service->handleUpload(UploadedFile::fake()->image('cover.jpg', 800, 600))->folder;
        $galleryA = $service->handleUpload(UploadedFile::fake()->image('a.jpg', 400, 400))->folder;
        $galleryB = $service->handleUpload(UploadedFile::fake()->image('b.jpg', 400, 400))->folder;

        $this->actingAs(actor_region())
            ->postJson(route('api.region.store'), [
                'name' => 'Elikobel',
                'cover' => $cover,
                'gallery' => [$galleryA, $galleryB],
            ])
            ->assertCreated()
            ->assertJsonCount(2, 'data.gallery')
            ->assertJsonPath('data.cover.id', fn ($id) => $id !== null);

        $region = Region::first();
        expect($region->getFirstMedia('cover'))->not->toBeNull()
            ->and($region->getMedia('gallery'))->toHaveCount(2);
    });

    it('menyinkronkan galeri: menghapus yang tidak dipertahankan lalu menambah baru', function () {
        Storage::fake('local');
        Storage::fake('public');

        $service = app(TemporaryMediaService::class);
        $region = Region::create(['name' => 'Ulilin '.uniqid()]);
        $region->addMediaFromTemporaryUpload(
            $service->handleUpload(UploadedFile::fake()->image('a.jpg', 400, 400))->folder,
            'gallery'
        );
        $region->addMediaFromTemporaryUpload(
            $service->handleUpload(UploadedFile::fake()->image('b.jpg', 400, 400))->folder,
            'gallery'
        );
        $keepId = $region->getMedia('gallery')->first()->id;
        $dropId = $region->getMedia('gallery')->last()->id;

        $newFolder = $service->handleUpload(UploadedFile::fake()->image('c.jpg', 400, 400))->folder;

        $this->actingAs(actor_region())
            ->putJson(route('api.region.update', $region->id), [
                'name' => $region->name,
                'retained_gallery' => [$keepId],
                'gallery' => [$newFolder],
            ])
            ->assertOk()
            ->assertJsonCount(2, 'data.gallery');

        $ids = $region->fresh()->getMedia('gallery')->pluck('id');
        expect($ids)->toContain($keepId)
            ->and($ids)->not->toContain($dropId)
            ->and($ids)->toHaveCount(2);
    });

    it('menghapus wilayah (soft delete)', function () {
        $region = Region::create(['name' => 'Hapus '.uniqid()]);

        $this->actingAs(actor_region())
            ->deleteJson(route('api.region.destroy', $region->id))
            ->assertOk();

        $this->assertSoftDeleted('regions', ['id' => $region->id]);
    });
});
