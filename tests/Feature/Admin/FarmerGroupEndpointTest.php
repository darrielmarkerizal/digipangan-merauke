<?php

use App\Models\User;
use Modules\Farmer\Models\FarmerGroup;
use Illuminate\Support\Facades\Hash;

function actor_farmer_group(string $role = 'super_admin'): User
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

describe('FarmerGroup CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.farmer_group.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_farmer_group('admin'))->getJson(route('api.farmer_group.index'))->assertOk();
    });
});
