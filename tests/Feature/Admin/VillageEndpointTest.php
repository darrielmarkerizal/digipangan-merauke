<?php

use App\Models\User;
use Modules\Region\Models\Village;
use Illuminate\Support\Facades\Hash;

function actor_village(string $role = 'super_admin'): User
{
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->assignRole($role);
    return $user;
}

use Modules\User\Database\Seeders\UserDatabaseSeeder;

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Village CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.village.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_village('admin'))->getJson(route('api.village.index'))->assertOk();
    });
});
