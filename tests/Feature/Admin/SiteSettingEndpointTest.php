<?php

use App\Models\User;
use Modules\Page\Database\Seeders\PageDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_site_setting(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    app(PageDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('SiteSetting', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.site_setting.index'))->assertStatus(401);
    });

    it('menolak pengguna tanpa izin kelola tentang dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.site_setting.index'))->assertStatus(403);
    });

    it('menampilkan seluruh pengaturan ter-seed', function () {
        $this->actingAs(actor_site_setting())
            ->getJson(route('api.site_setting.index'))
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['key' => 'admin_contact_email', 'type' => 'email']);
    });

    it('memperbarui sebagian pengaturan dan menyimpannya', function () {
        $this->actingAs(actor_site_setting())
            ->putJson(route('api.site_setting.update'), [
                'about_purpose' => 'Tujuan baru DigiPangan.',
                'admin_contact_email' => 'admin@digipangan.test',
            ])
            ->assertOk()
            ->assertJsonFragment(['key' => 'admin_contact_email', 'value' => 'admin@digipangan.test']);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'admin_contact_email',
            'value' => 'admin@digipangan.test',
        ]);
        $this->assertDatabaseHas('site_settings', [
            'key' => 'about_purpose',
            'value' => 'Tujuan baru DigiPangan.',
        ]);
    });

    it('menolak email admin tidak valid dengan 422', function () {
        $this->actingAs(actor_site_setting())
            ->putJson(route('api.site_setting.update'), ['admin_contact_email' => 'bukan-email'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('admin_contact_email');
    });

    it('menolak key yang tidak dikenal dengan 422', function () {
        $this->actingAs(actor_site_setting())
            ->putJson(route('api.site_setting.update'), ['key_asing' => 'nilai'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('key_asing');
    });
});
