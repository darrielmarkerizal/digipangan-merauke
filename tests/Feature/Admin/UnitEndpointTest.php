<?php

use App\Models\User;
use Modules\Product\Models\Unit;
use Illuminate\Support\Facades\Hash;

function actor_unit(string $role = 'super_admin'): User
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

describe('Unit CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.unit.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_unit('admin'))->getJson(route('api.unit.index'))->assertOk();
    });
});
