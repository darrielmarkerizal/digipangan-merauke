<?php

use App\Models\User;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostCategory;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_post_category(string $role = 'super_admin'): User
{
    $user = User::factory()->create([
        'is_active' => true,
    ]);
    $user->assignRole($role);

    return $user;
}

function post_category_referencing_post(PostCategory $category): Post
{
    return Post::create([
        'post_category_id' => $category->id,
        'author_id' => User::factory()->create(['is_active' => true])->id,
        'title' => 'Berita '.uniqid(),
        'body' => 'Isi berita contoh.',
    ]);
}

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

    it('menolak pengguna tanpa izin kelola master data dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.post_category.index'))->assertStatus(403);
    });

    it('membuat kategori berita baru', function () {
        $this->actingAs(actor_post_category())
            ->postJson(route('api.post_category.store'), ['name' => 'Panen'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Panen');

        $this->assertDatabaseHas('post_categories', ['name' => 'Panen']);
    });

    it('menolak nama duplikat dengan 422', function () {
        PostCategory::create(['name' => 'Panen']);

        $this->actingAs(actor_post_category())
            ->postJson(route('api.post_category.store'), ['name' => 'Panen'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    });

    it('memperbarui kategori berita', function () {
        $category = PostCategory::create(['name' => 'Pelatihan']);

        $this->actingAs(actor_post_category())
            ->putJson(route('api.post_category.update', $category->id), ['name' => 'Pelatihan Petani'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Pelatihan Petani');
    });

    it('menghapus kategori berita yang tidak direferensikan', function () {
        $category = PostCategory::create(['name' => 'Harga Pasar']);

        $this->actingAs(actor_post_category())
            ->deleteJson(route('api.post_category.destroy', $category->id))
            ->assertOk();

        $this->assertDatabaseMissing('post_categories', ['id' => $category->id]);
    });

    it('menolak menghapus kategori berita yang masih dipakai berita dengan 409', function () {
        $category = PostCategory::create(['name' => 'Kegiatan']);
        post_category_referencing_post($category);

        $this->actingAs(actor_post_category())
            ->deleteJson(route('api.post_category.destroy', $category->id))
            ->assertStatus(409);

        $this->assertDatabaseHas('post_categories', ['id' => $category->id]);
    });
});
