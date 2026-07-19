<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function makeSearchUser(string $name, string $email, string $role = 'admin'): User
{
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make('rahasia123'),
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user->fresh();
}

function search(string $query): array
{
    return test()->getJson(route('api.user.index').'?search='.urlencode($query))->json();
}

beforeEach(function () {
    config(['digipangan.admin.email' => null]);
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));

    $this->actingAs(makeSearchUser('Pengelola Utama', 'pengelola@digipangan.test', 'super_admin'));

    makeSearchUser('Budi Santoso', 'budi@muting.test');
    makeSearchUser('Budi Hartono', 'hartono@ulilin.test');
    makeSearchUser('Siti Rahayu', 'siti@elikobel.test');
});

it('mencari lintas kolom nama dan email', function () {
    expect(search('budi')['meta']['total'])->toBe(2)
        ->and(search('elikobel')['meta']['total'])->toBe(1);
});

it('mengharuskan semua kata cocok, boleh di kolom berbeda', function () {
    $hasil = search('budi ulilin');

    expect($hasil['meta']['total'])->toBe(1)
        ->and($hasil['data'][0]['name'])->toBe('Budi Hartono');
});

it('tidak mengembalikan hasil bila salah satu kata tidak cocok', function () {
    expect(search('budi zzzz')['meta']['total'])->toBe(0);
});

it('mencari lewat kolom relasi', function () {
    expect(search('super_admin')['meta']['total'])->toBe(1);
});

it('mencari sebagian kata di tengah', function () {
    expect(search('anto')['meta']['total'])->toBe(1)
        ->and(search('anto')['data'][0]['name'])->toBe('Budi Santoso');
});

it('tidak peka huruf besar-kecil', function () {
    expect(search('BUDI')['meta']['total'])->toBe(search('budi')['meta']['total']);
});

it('memperlakukan persen sebagai teks biasa, bukan wildcard', function () {
    makeSearchUser('Diskon 50% Panen', 'diskon@digipangan.test');

    expect(search('50%')['meta']['total'])->toBe(1)
        ->and(search('50%')['data'][0]['name'])->toBe('Diskon 50% Panen')
        ->and(search('%a')['meta']['total'])->toBe(0);
});

it('memperlakukan garis bawah sebagai teks biasa', function () {
    makeSearchUser('Petani_Khusus', 'khusus@digipangan.test');

    expect(search('i_K')['meta']['total'])->toBe(1);
});

it('mengabaikan kata yang terlalu pendek', function () {
    expect(search('a')['meta']['total'])->toBe(User::count());
});

it('mengabaikan pencarian kosong', function () {
    expect(search('   ')['meta']['total'])->toBe(User::count());
});

it('mengutamakan kecocokan persis di urutan teratas', function () {
    makeSearchUser('Budi', 'budi.persis@digipangan.test');

    expect(search('Budi')['data'][0]['name'])->toBe('Budi');
});

it('tidak menimpa urutan yang diminta pengguna', function () {
    $names = collect(
        $this->getJson(route('api.user.index').'?search=budi&sort=name')->json('data')
    )->pluck('name');

    expect($names->all())->toBe(['Budi Hartono', 'Budi Santoso']);
});

it('tetap dapat digabung dengan filter lain', function () {
    User::where('email', 'budi@muting.test')->update(['is_active' => false]);

    $hasil = $this->getJson(route('api.user.index').'?search=budi&filter[is_active]=1')->json();

    expect($hasil['meta']['total'])->toBe(1)
        ->and($hasil['data'][0]['name'])->toBe('Budi Hartono');
});
