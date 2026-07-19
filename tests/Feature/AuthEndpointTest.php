<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function makeUser(array $overrides = [], string $role = 'admin'): User
{
    $user = User::create(array_merge([
        'name' => 'Admin Satu',
        'email' => 'admin.satu@digipangan.test',
        'password' => Hash::make('rahasia123'),
        'is_active' => true,
    ], $overrides));

    $user->assignRole($role);

    return $user->fresh();
}

beforeEach(function () {
    config(['digipangan.admin.email' => null]);
    app(UserDatabaseSeeder::class)->run();

    $this->withHeader('Origin', config('app.url'));
});

describe('login', function () {
    it('menerima kredensial benar dan mengembalikan profil', function () {
        makeUser();

        $this->postJson(route('api.auth.login'), [
            'email' => 'admin.satu@digipangan.test',
            'password' => 'rahasia123',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'admin.satu@digipangan.test')
            ->assertJsonPath('data.roles.0', 'admin');

        expect(auth()->check())->toBeTrue();
    });

    it('menolak kata sandi salah dengan 422', function () {
        makeUser();

        $this->postJson(route('api.auth.login'), [
            'email' => 'admin.satu@digipangan.test',
            'password' => 'salah',
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors('email');

        expect(auth()->check())->toBeFalse();
    });

    it('menolak akun nonaktif meski kata sandinya benar', function () {
        makeUser(['is_active' => false]);

        $this->postJson(route('api.auth.login'), [
            'email' => 'admin.satu@digipangan.test',
            'password' => 'rahasia123',
        ])->assertStatus(422);

        expect(auth()->check())->toBeFalse();
    });

    it('menolak permintaan tanpa email atau kata sandi', function () {
        $this->postJson(route('api.auth.login'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    });
});

describe('profil', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.auth.profile'))
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    });

    it('mengembalikan role dan permission milik pengguna', function () {
        $this->actingAs(makeUser())
            ->getJson(route('api.auth.profile'))
            ->assertOk()
            ->assertJsonPath('data.roles.0', 'admin')
            ->assertJsonPath('data.avatar_url', null)
            ->assertJsonFragment(['produk.kelola']);
    });

    it('tidak membocorkan kata sandi', function () {
        $this->actingAs(makeUser())
            ->getJson(route('api.auth.profile'))
            ->assertOk()
            ->assertJsonMissingPath('data.password');
    });

    it('memperbarui nama dan avatar', function () {
        Storage::fake('public');
        $user = makeUser();

        $this->actingAs($user)
            ->putJson(route('api.auth.profile.update'), [
                'name' => 'Nama Baru',
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 600, 600),
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru');

        expect($user->fresh()->getFirstMedia('avatar'))->not->toBeNull()
            ->and($user->fresh()->avatarUrl())->not->toBeNull();
    });

    it('menolak berkas avatar yang bukan gambar', function () {
        Storage::fake('public');

        $this->actingAs(makeUser())
            ->putJson(route('api.auth.profile.update'), [
                'avatar' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('avatar');
    });

    it('hanya menyimpan satu avatar meski diunggah berkali-kali', function () {
        Storage::fake('public');
        $user = makeUser();

        foreach (['satu.jpg', 'dua.jpg'] as $name) {
            $this->actingAs($user)->putJson(route('api.auth.profile.update'), [
                'avatar' => UploadedFile::fake()->image($name, 400, 400),
            ])->assertOk();
        }

        expect($user->fresh()->getMedia('avatar'))->toHaveCount(1);
    });
});

describe('logout', function () {
    it('mengakhiri sesi sehingga profil tidak lagi dapat diakses', function () {
        makeUser();

        $this->postJson(route('api.auth.login'), [
            'email' => 'admin.satu@digipangan.test',
            'password' => 'rahasia123',
        ])->assertOk();

        $this->getJson(route('api.auth.profile'))->assertOk();
        $this->assertAuthenticated('web');

        $this->postJson(route('api.auth.logout'))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertGuest('web');
    });
});
