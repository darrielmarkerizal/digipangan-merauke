<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Services\TemporaryMediaService;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostCategory;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

it('web admin can create post with image and video in content_media', function () {
    Storage::fake('local');
    Storage::fake('public');

    $author = User::factory()->create(['is_active' => true]);
    $author->assignRole('super_admin');
    $category = PostCategory::create(['name' => 'Kategori Web']);

    $folder = app(TemporaryMediaService::class)
        ->handleUpload(UploadedFile::fake()->image('kegiatan.jpg', 800, 600))
        ->folder;

    $body = '<p>Paragraf 1</p><p data-temp-media="'.$folder.'"><img src="blob:http://localhost/test" alt="test"></p><p>Paragraf 2</p>';

    $response = $this->actingAs($author)->post('/admin/berita', [
        'post_category_id' => $category->id,
        'title' => 'Judul Berita Web',
        'body' => $body,
        'status' => 'published',
        'content_media' => [$folder],
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/berita');
});

it('web admin can update post with image in content_media', function () {
    Storage::fake('local');
    Storage::fake('public');

    $author = User::factory()->create(['is_active' => true]);
    $author->assignRole('super_admin');
    $category = PostCategory::create(['name' => 'Kategori Web']);

    $post = Post::create([
        'post_category_id' => $category->id,
        'author_id' => $author->id,
        'title' => 'Judul Awal',
        'body' => '<p>Isi awal</p>',
        'status' => 'published',
    ]);

    $folder = app(TemporaryMediaService::class)
        ->handleUpload(UploadedFile::fake()->image('kegiatan2.jpg', 800, 600))
        ->folder;

    $body = '<p>Isi baru</p><p data-temp-media="'.$folder.'"><img src="blob:http://localhost/test" alt="test"></p>';

    $response = $this->actingAs($author)->put('/admin/berita/'.$post->id, [
        'post_category_id' => $category->id,
        'title' => 'Judul Diperbarui',
        'body' => $body,
        'status' => 'published',
        'content_media' => [$folder],
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/berita');
});
