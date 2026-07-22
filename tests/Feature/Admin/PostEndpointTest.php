<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Services\TemporaryMediaService;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostCategory;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_post(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

function post_category(): PostCategory
{
    return PostCategory::create(['name' => 'Panen '.uniqid()]);
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Post CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.post.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_post('admin'))->getJson(route('api.post.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola berita dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.post.index'))->assertStatus(403);
    });

    it('membuat berita dan mengisi author dari user login', function () {
        $author = actor_post();
        $category = post_category();

        $this->actingAs($author)
            ->postJson(route('api.post.store'), [
                'post_category_id' => $category->id,
                'title' => 'Panen Raya Cabai',
                'body' => 'Isi berita panen.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Panen Raya Cabai')
            ->assertJsonPath('data.author.id', $author->id)
            ->assertJsonPath('data.status', 'draft');

        $this->assertDatabaseHas('posts', ['title' => 'Panen Raya Cabai', 'author_id' => $author->id]);
    });

    it('mengisi published_at otomatis saat status published', function () {
        $category = post_category();

        $this->actingAs(actor_post())
            ->postJson(route('api.post.store'), [
                'post_category_id' => $category->id,
                'title' => 'Berita Terbit',
                'body' => 'Isi.',
                'status' => 'published',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'published')
            ->assertJsonPath('data.published_at', fn ($value) => $value !== null);
    });

    it('menolak kategori yang tidak ada dengan 422', function () {
        $this->actingAs(actor_post())
            ->postJson(route('api.post.store'), [
                'post_category_id' => 999999,
                'title' => 'Berita',
                'body' => 'Isi.',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('post_category_id');
    });

    it('memperbarui berita', function () {
        $author = actor_post();
        $post = Post::create([
            'post_category_id' => post_category()->id,
            'author_id' => $author->id,
            'title' => 'Judul Lama',
            'body' => 'Isi lama.',
        ]);

        $this->actingAs($author)
            ->putJson(route('api.post.update', $post->id), [
                'post_category_id' => $post->post_category_id,
                'title' => 'Judul Baru',
                'body' => 'Isi baru.',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Judul Baru');
    });

    it('melampirkan cover dari unggahan sementara', function () {
        Storage::fake('local');
        Storage::fake('public');

        $category = post_category();
        $cover = app(TemporaryMediaService::class)
            ->handleUpload(UploadedFile::fake()->image('panen.jpg', 800, 600))
            ->folder;

        $this->actingAs(actor_post())
            ->postJson(route('api.post.store'), [
                'post_category_id' => $category->id,
                'title' => 'Berita Bergambar',
                'body' => 'Isi.',
                'cover' => $cover,
            ])
            ->assertCreated()
            ->assertJsonPath('data.cover.id', fn ($id) => $id !== null);

        expect(Post::first()->getFirstMedia('cover'))->not->toBeNull();
    });

    it('menghapus berita (soft delete)', function () {
        $author = actor_post();
        $post = Post::create([
            'post_category_id' => post_category()->id,
            'author_id' => $author->id,
            'title' => 'Judul Hapus',
            'body' => 'Isi.',
        ]);

        $this->actingAs($author)
            ->deleteJson(route('api.post.destroy', $post->id))
            ->assertOk();

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    });
});
