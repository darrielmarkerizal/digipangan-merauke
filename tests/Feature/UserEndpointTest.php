<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        ])->assignRole('admin');
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

    it('menolak admin biasa dengan 403', function () {
        $this->actingAs(actor('admin'))
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

        $this->postJson(route('api.user.store'), [
            'name' => 'Petugas Baru',
            'email' => 'baru@digipangan.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'roles' => ['admin'],
            'avatar' => UploadedFile::fake()->image('foto.jpg', 300, 300),
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Petugas Baru')
            ->assertJsonPath('data.roles.0', 'admin');

        $created = User::where('email', 'baru@digipangan.test')->first();

        expect($created->getFirstMedia('avatar'))->not->toBeNull()
            ->and(Hash::check('rahasia123', $created->password))->toBeTrue();
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
