<?php

use Modules\Media\Models\TemporaryFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Page\Models\Partner;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('can upload temporary media', function () {
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

    Storage::disk('local')->assertExists('temp/' . $folder . '/' . $filename);
});

it('can delete temporary media', function () {
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

    Storage::disk('local')->assertMissing('temp/' . $folder . '/' . $file->getClientOriginalName());
});

it('can attach temporary media to a model using the trait', function () {
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
    Storage::disk('local')->assertMissing('temp/' . $folder . '/' . $filename);
});
