<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function runUserSeeder(): void
{
    app(UserDatabaseSeeder::class)->run();
}

beforeEach(function () {
    config([
        'digipangan.admin.name' => 'Administrator',
        'digipangan.admin.email' => 'admin@digipangan.test',
        'digipangan.admin.password' => 'password',
    ]);
});

it('membuat akun admin dari konfigurasi', function () {
    runUserSeeder();

    $admin = User::where('email', 'admin@digipangan.test')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->name)->toBe('Administrator')
        ->and($admin->is_active)->toBeTrue()
        ->and($admin->hasRole('super_admin'))->toBeTrue();
});

it('menyimpan password dalam bentuk hash, bukan teks polos', function () {
    runUserSeeder();

    $admin = User::where('email', 'admin@digipangan.test')->first();

    expect($admin->password)->not->toBe('password')
        ->and(Hash::check('password', $admin->password))->toBeTrue();
});

it('melewati pembuatan akun ketika kredensial belum diisi', function () {
    config(['digipangan.admin.email' => null, 'digipangan.admin.password' => null]);

    runUserSeeder();

    expect(User::count())->toBe(0);
});

it('tidak menimpa akun yang sudah ada saat seed diulang', function () {
    runUserSeeder();
    User::where('email', 'admin@digipangan.test')->update(['name' => 'Nama Diganti Manual']);

    runUserSeeder();

    expect(User::count())->toBe(1)
        ->and(User::first()->name)->toBe('Nama Diganti Manual');
});

it('menolak password lemah ketika dijalankan di produksi', function () {
    app()->detectEnvironment(fn () => 'production');

    expect(fn () => runUserSeeder())
        ->toThrow(RuntimeException::class, 'ADMIN_PASSWORD masih memakai nilai default yang lemah');

    expect(User::count())->toBe(0);
});

it('mengizinkan password kuat di produksi', function () {
    app()->detectEnvironment(fn () => 'production');
    config(['digipangan.admin.password' => 'K0pi-Merauke!2026']);

    runUserSeeder();

    expect(User::where('email', 'admin@digipangan.test')->exists())->toBeTrue();
});
