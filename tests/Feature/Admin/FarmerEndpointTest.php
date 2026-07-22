<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Farmer\Models\Commodity;
use Modules\Farmer\Models\Farmer;
use Modules\Media\Services\TemporaryMediaService;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_farmer(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

function farmer_region(): Region
{
    return Region::create(['name' => 'Elikobel '.uniqid()]);
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Farmer CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.farmer.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_farmer('admin'))->getJson(route('api.farmer.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola petani dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.farmer.index'))->assertStatus(403);
    });

    it('membuat petani baru', function () {
        $region = farmer_region();

        $this->actingAs(actor_farmer())
            ->postJson(route('api.farmer.store'), [
                'region_id' => $region->id,
                'name' => 'Muhamad Riam',
                'phone' => '+6281234567890',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Muhamad Riam')
            ->assertJsonPath('data.region.id', $region->id);

        $this->assertDatabaseHas('farmers', ['name' => 'Muhamad Riam', 'region_id' => $region->id]);
    });

    it('menolak wilayah yang tidak ada dengan 422', function () {
        $this->actingAs(actor_farmer())
            ->postJson(route('api.farmer.store'), [
                'region_id' => 999999,
                'name' => 'Petani',
                'phone' => '+6281234567890',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('region_id');
    });

    it('menyinkronkan komoditas petani', function () {
        $region = farmer_region();
        $cabai = Commodity::create(['name' => 'Cabai '.uniqid()]);
        $tomat = Commodity::create(['name' => 'Tomat '.uniqid()]);

        $this->actingAs(actor_farmer())
            ->postJson(route('api.farmer.store'), [
                'region_id' => $region->id,
                'name' => 'Muhamad Riam',
                'phone' => '+6281234567890',
                'commodities' => [$cabai->id, $tomat->id],
            ])
            ->assertCreated()
            ->assertJsonCount(2, 'data.commodities');

        expect(Farmer::first()->commodities)->toHaveCount(2);
    });

    it('memperbarui petani dan mengubah komoditas', function () {
        $region = farmer_region();
        $cabai = Commodity::create(['name' => 'Cabai '.uniqid()]);
        $kopi = Commodity::create(['name' => 'Kopi '.uniqid()]);
        $farmer = Farmer::create(['region_id' => $region->id, 'name' => 'Petani Lama', 'phone' => '+6281111111111']);
        $farmer->commodities()->sync([$cabai->id]);

        $this->actingAs(actor_farmer())
            ->putJson(route('api.farmer.update', $farmer->id), [
                'region_id' => $region->id,
                'name' => 'Petani Baru',
                'phone' => '+6282222222222',
                'commodities' => [$kopi->id],
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Petani Baru')
            ->assertJsonCount(1, 'data.commodities');

        expect($farmer->fresh()->commodities->pluck('id')->all())->toBe([$kopi->id]);
    });

    it('melampirkan foto dari unggahan sementara', function () {
        Storage::fake('local');
        Storage::fake('public');

        $region = farmer_region();
        $folder = app(TemporaryMediaService::class)
            ->handleUpload(UploadedFile::fake()->image('riam.jpg', 500, 500))
            ->folder;

        $this->actingAs(actor_farmer())
            ->postJson(route('api.farmer.store'), [
                'region_id' => $region->id,
                'name' => 'Muhamad Riam',
                'phone' => '+6281234567890',
                'photo' => $folder,
            ])
            ->assertCreated()
            ->assertJsonPath('data.photo.id', fn ($id) => $id !== null);

        expect(Farmer::first()->getFirstMedia('photo'))->not->toBeNull();
    });

    it('menghapus petani (soft delete)', function () {
        $region = farmer_region();
        $farmer = Farmer::create(['region_id' => $region->id, 'name' => 'Petani Hapus', 'phone' => '+6283333333333']);

        $this->actingAs(actor_farmer())
            ->deleteJson(route('api.farmer.destroy', $farmer->id))
            ->assertOk();

        $this->assertSoftDeleted('farmers', ['id' => $farmer->id]);
    });
});
