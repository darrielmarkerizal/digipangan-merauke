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
    config(['filesystems.default' => 'public']);

    $author = User::factory()->create(['is_active' => true]);
    $author->assignRole('super_admin');
    $category = PostCategory::create(['name' => 'Kategori Web']);

    $mediaService = app(TemporaryMediaService::class);
    $coverFolder = $mediaService
        ->handleUpload(UploadedFile::fake()->image('cover.jpg', 800, 600))
        ->folder;
    $imageFolder = $mediaService
        ->handleUpload(UploadedFile::fake()->image('kegiatan.jpg', 800, 600))
        ->folder;
    $videoFolder = $mediaService
        ->handleUpload(UploadedFile::fake()->create('edukasi.mp4', 512, 'video/mp4'))
        ->folder;

    $body = '<p>Paragraf 1</p>'
        .'<p data-temp-media="'.$imageFolder.'"><img src="blob:http://localhost/image" alt="test"></p>'
        .'<p data-temp-media="'.$videoFolder.'"><video controls src="blob:http://localhost/video"></video></p>'
        .'<p>Paragraf 2</p>';

    $response = $this->actingAs($author)->post('/admin/berita', [
        'post_category_id' => $category->id,
        'title' => 'Judul Berita Web',
        'body' => $body,
        'status' => 'published',
        'cover' => $coverFolder,
        'content_media' => [$imageFolder, $videoFolder],
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/admin/berita');

    $post = Post::where('title', 'Judul Berita Web')->firstOrFail();
    expect($post->getFirstMedia('cover'))->not->toBeNull()
        ->and($post->getMedia('content_media'))->toHaveCount(2)
        ->and($post->body)->toContain('<video')
        ->and($post->body)->not->toContain('data-temp-media');
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
