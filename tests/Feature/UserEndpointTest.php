<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Farmer\Models\Farmer;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor(string $role = 'super_admin'): User
{
    $user = User::create([
        'name' => 'Pengelola',
        'email' => "{$role}@digipangan.test",
        'password' => Hash::make('rahasia123'),
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user->fresh();
}

function seedUsers(int $count = 3): void
{
    foreach (range(1, $count) as $i) {
        User::create([
            'name' => "Petugas {$i}",
            'email' => "petugas{$i}@digipangan.test",
            'password' => Hash::make('rahasia123'),
            'is_active' => $i % 2 === 1,
        ])->assignRole('farmer');
    }
}

beforeEach(function () {
    config(['digipangan.admin.email' => null]);
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('otorisasi', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.user.index'))->assertStatus(401);
    });

    it('menolak admin distrik dari pengelolaan pengguna dengan 403', function () {
        $this->actingAs(actor('admin_distrik'))
            ->getJson(route('api.user.index'))
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    });

    it('mengizinkan super admin', function () {
        $this->actingAs(actor())->getJson(route('api.user.index'))->assertOk();
    });
});

describe('daftar pengguna', function () {
    beforeEach(fn () => $this->actingAs(actor()));

    it('mengembalikan struktur paginasi', function () {
        seedUsers();

        $this->getJson(route('api.user.index'))
            ->assertOk()
            ->assertJsonStructure([
                'success', 'message',
                'data' => [['id', 'name', 'email', 'is_active', 'avatar_url']],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['first', 'last', 'prev', 'next'],
            ]);
    });

    it('menyaring berdasarkan nama secara parsial', function () {
        seedUsers();

        $response = $this->getJson(route('api.user.index').'?filter[name]=Petugas 2')->assertOk();

        expect($response->json('meta.total'))->toBe(1)
            ->and($response->json('data.0.name'))->toBe('Petugas 2');
    });

    it('menyaring berdasarkan status aktif', function () {
        seedUsers(4);

        $response = $this->getJson(route('api.user.index').'?filter[is_active]=0')->assertOk();

        expect(collect($response->json('data'))->pluck('is_active')->unique()->all())->toBe([false]);
    });

    it('menyaring berdasarkan peran', function () {
        seedUsers();

        $response = $this->getJson(route('api.user.index').'?filter[role]=super_admin')->assertOk();

        expect($response->json('meta.total'))->toBe(1);
    });

    it('mengurutkan berdasarkan nama', function () {
        seedUsers();

        $names = collect($this->getJson(route('api.user.index').'?sort=name')->json('data'))->pluck('name');

        expect($names->all())->toBe($names->sort()->values()->all());
    });

    it('menyertakan relasi peran saat diminta', function () {
        seedUsers(1);

        $this->getJson(route('api.user.index').'?include=roles')
            ->assertOk()
            ->assertJsonStructure(['data' => [['roles']]]);
    });

    it('menolak filter yang tidak diizinkan dengan 400', function () {
        $this->getJson(route('api.user.index').'?filter[password]=rahasia')
            ->assertStatus(400)
            ->assertJsonPath('success', false);
    });

    it('membatasi per_page pada nilai maksimum', function () {
        seedUsers(3);

        expect($this->getJson(route('api.user.index').'?per_page=5000')->json('meta.per_page'))->toBe(100);
    });
});

describe('membuat dan mengubah pengguna', function () {
    beforeEach(fn () => $this->actingAs(actor()));

    it('membuat pengguna beserta peran dan avatar', function () {
        Storage::fake('public');
        Storage::fake('local');

        $upload = $this->postJson(route('media.upload'), [
            'file' => UploadedFile::fake()->image('foto.jpg', 300, 300),
        ]);

        $this->postJson(route('api.user.store'), [
            'name' => 'Petugas Baru',
            'email' => 'baru@digipangan.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'roles' => ['super_admin'],
            'avatar_uuid' => $upload->json('folder'),
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Petugas Baru')
            ->assertJsonPath('data.roles.0', 'super_admin');

        $created = User::where('email', 'baru@digipangan.test')->first();

        expect($created->getFirstMedia('avatar'))->not->toBeNull()
            ->and(Hash::check('rahasia123', $created->password))->toBeTrue();
    });

    it('membuat profil petani terhubung saat akun petani dibuat admin', function () {
        $region = Region::create(['name' => 'Distrik Akun Petani']);

        $response = $this->postJson(route('api.user.store'), [
            'name' => 'Petani Baru',
            'email' => 'petani.baru@digipangan.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'roles' => ['farmer'],
            'region_id' => $region->id,
            'phone' => '081234567890',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.roles.0', 'farmer')
            ->assertJsonPath('data.farmer.phone', '081234567890');

        $created = User::where('email', 'petani.baru@digipangan.test')->firstOrFail();

        expect($created->farmer)->not->toBeNull()
            ->and($created->farmer->user_id)->toBe($created->id)
            ->and($created->farmer->region_id)->toBe($region->id);

        $this->actingAs($created)->get('/petani/dashboard')->assertOk();
    });

    it('dapat memperbaiki akun petani lama yang belum memiliki profil', function () {
        $region = Region::create(['name' => 'Distrik Repair Petani']);
        $target = User::create([
            'name' => 'Petani Lama',
            'email' => 'petani.lama@digipangan.test',
            'password' => Hash::make('rahasia123'),
            'is_active' => true,
        ]);
        $target->assignRole('farmer');

        expect(Farmer::where('user_id', $target->id)->exists())->toBeFalse();

        $this->putJson(route('api.user.update', $target), [
            'name' => 'Petani Lama',
            'roles' => ['farmer'],
            'region_id' => $region->id,
            'phone' => '081298765432',
        ])->assertOk()->assertJsonPath('data.farmer.region_id', $region->id);

        expect($target->fresh()->farmer)->not->toBeNull();

        $this->actingAs($target->fresh())->get('/petani/dashboard')->assertOk();
    });

    it('menolak email duplikat', function () {
        seedUsers(1);

        $this->postJson(route('api.user.store'), [
            'name' => 'Duplikat',
            'email' => 'petugas1@digipangan.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    });

    it('menolak kata sandi tanpa konfirmasi yang cocok', function () {
        $this->postJson(route('api.user.store'), [
            'name' => 'Tanpa Konfirmasi',
            'email' => 'lain@digipangan.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'berbeda',
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    });

    it('menolak role legacy admin', function () {
        $this->postJson(route('api.user.store'), [
            'name' => 'Role Legacy',
            'email' => 'legacy@digipangan.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'roles' => ['admin'],
        ])->assertStatus(422)->assertJsonValidationErrors('roles.0');
    });

    it('mengubah nama tanpa menyentuh kata sandi', function () {
        seedUsers(1);
        $target = User::where('email', 'petugas1@digipangan.test')->first();
        $hashLama = $target->password;

        $this->putJson(route('api.user.update', $target), ['name' => 'Nama Diubah'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Diubah');

        expect($target->fresh()->password)->toBe($hashLama);
    });

    it('menonaktifkan pengguna', function () {
        seedUsers(1);
        $target = User::where('email', 'petugas1@digipangan.test')->first();

        $this->putJson(route('api.user.update', $target), ['is_active' => false])
            ->assertOk()
            ->assertJsonPath('data.is_active', false);
    });
});

describe('menghapus pengguna', function () {
    it('menghapus pengguna lain secara soft delete', function () {
        $this->actingAs(actor());
        seedUsers(1);
        $target = User::where('email', 'petugas1@digipangan.test')->first();

        $this->deleteJson(route('api.user.destroy', $target))->assertOk();

        expect(User::find($target->id))->toBeNull()
            ->and(User::withTrashed()->find($target->id))->not->toBeNull();
    });

    it('menolak menghapus akun sendiri', function () {
        $me = actor();

        $this->actingAs($me)
            ->deleteJson(route('api.user.destroy', $me))
            ->assertStatus(422);

        expect(User::find($me->id))->not->toBeNull();
    });
});
