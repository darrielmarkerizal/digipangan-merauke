<?php

use App\Models\User;
use Modules\Post\Models\PostCategory;
use Illuminate\Support\Facades\Hash;

function actor_post_category(string $role = 'super_admin'): User
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

describe('PostCategory CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.post_category.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_post_category('admin'))->getJson(route('api.post_category.index'))->assertOk();
    });
});
