<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Services\TemporaryMediaService;
use Modules\Page\Models\Partner;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_partner(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Partner CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.partner.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_partner('admin'))->getJson(route('api.partner.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola tentang dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.partner.index'))->assertStatus(403);
    });

    it('membuat mitra baru', function () {
        $this->actingAs(actor_partner())
            ->postJson(route('api.partner.store'), [
                'name' => 'Universitas Gadjah Mada',
                'website_url' => 'https://ugm.ac.id',
                'sort_order' => 1,
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Universitas Gadjah Mada');

        $this->assertDatabaseHas('partners', ['name' => 'Universitas Gadjah Mada']);
    });

    it('menolak nama duplikat dengan 422', function () {
        Partner::create(['name' => 'UGM']);

        $this->actingAs(actor_partner())
            ->postJson(route('api.partner.store'), ['name' => 'UGM'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('menolak website_url tidak valid dengan 422', function () {
        $this->actingAs(actor_partner())
            ->postJson(route('api.partner.store'), ['name' => 'Mitra', 'website_url' => 'bukan-url'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('website_url');
    });

    it('memperbarui mitra', function () {
        $partner = Partner::create(['name' => 'Mitra Lama']);

        $this->actingAs(actor_partner())
            ->putJson(route('api.partner.update', $partner->id), ['name' => 'Mitra Baru'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Mitra Baru');
    });

    it('mengurutkan daftar berdasarkan sort_order', function () {
        Partner::create(['name' => 'Kedua', 'sort_order' => 2]);
        Partner::create(['name' => 'Pertama', 'sort_order' => 1]);

        $this->actingAs(actor_partner())
            ->getJson(route('api.partner.index'))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Pertama')
            ->assertJsonPath('data.1.name', 'Kedua');
    });

    it('melampirkan logo dari unggahan sementara', function () {
        Storage::fake('local');
        Storage::fake('public');

        $logo = app(TemporaryMediaService::class)
            ->handleUpload(UploadedFile::fake()->image('logo.png', 300, 300))
            ->folder;

        $this->actingAs(actor_partner())
            ->postJson(route('api.partner.store'), ['name' => 'Mitra Logo', 'logo' => $logo])
            ->assertCreated()
            ->assertJsonPath('data.logo.id', fn ($id) => $id !== null);

        expect(Partner::first()->getFirstMedia('logo'))->not->toBeNull();
    });

    it('menghapus mitra (soft delete)', function () {
        $partner = Partner::create(['name' => 'Mitra Hapus']);

        $this->actingAs(actor_partner())
            ->deleteJson(route('api.partner.destroy', $partner->id))
            ->assertOk();

        $this->assertSoftDeleted('partners', ['id' => $partner->id]);
    });
});
