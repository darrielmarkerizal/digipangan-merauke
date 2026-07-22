<?php

use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Page\Models\Partner;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('menolak unggahan dari tamu dengan 401', function () {
    $this->postJson(route('api.media.upload'), [
        'file' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertStatus(401);
});

it('can upload temporary media', function () {
    $this->actingAs(User::factory()->create(['is_active' => true]));

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson(route('api.media.upload'), [
        'file' => $file,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['folder', 'filename']);

    $folder = $response->json('folder');
    $filename = $response->json('filename');

    $this->assertDatabaseHas('temporary_files', [
        'folder' => $folder,
        'filename' => $filename,
    ]);

    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('local');
    $disk->assertExists('temp/'.$folder.'/'.$filename);
});

it('can delete temporary media', function () {
    $this->actingAs(User::factory()->create(['is_active' => true]));

    $file = UploadedFile::fake()->image('avatar.jpg');

    $uploadResponse = $this->postJson(route('api.media.upload'), [
        'file' => $file,
    ]);

    $folder = $uploadResponse->json('folder');

    $deleteResponse = $this->deleteJson(route('api.media.delete'), [
        'folder' => $folder,
    ]);

    $deleteResponse->assertStatus(200);

    $this->assertDatabaseMissing('temporary_files', [
        'folder' => $folder,
    ]);

    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('local');
    $disk->assertMissing('temp/'.$folder.'/'.$file->getClientOriginalName());
});

it('can attach temporary media to a model using the trait', function () {
    $this->actingAs(User::factory()->create(['is_active' => true]));

    $file = UploadedFile::fake()->image('logo.png');

    $uploadResponse = $this->postJson(route('api.media.upload'), [
        'file' => $file,
    ]);

    $folder = $uploadResponse->json('folder');
    $filename = $uploadResponse->json('filename');

    $partner = Partner::create([
        'name' => 'Test Partner',
        'is_active' => true,
    ]);

    $media = $partner->addMediaFromTemporaryUpload($folder, 'logo');

    expect($media)->not->toBeNull();
    expect($partner->getMedia('logo')->count())->toBe(1);

    // Verify temp file is cleaned up
    $this->assertDatabaseMissing('temporary_files', [
        'folder' => $folder,
    ]);
    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('local');
    $disk->assertMissing('temp/'.$folder.'/'.$filename);
});
