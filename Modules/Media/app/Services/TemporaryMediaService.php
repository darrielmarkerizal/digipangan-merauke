<?php

namespace Modules\Media\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Media\Models\TemporaryFile;
use Modules\Media\Repositories\TemporaryFileRepositoryInterface;
use Spatie\Image\Image;

class TemporaryMediaService
{
    private const MAX_IMAGE_DIMENSION = 2048;

    public function __construct(
        private TemporaryFileRepositoryInterface $repository
    ) {}

    public function handleUpload(UploadedFile $file): TemporaryFile
    {
        $filename = $file->hashName();
        $folder = (string) Str::uuid();

        $storedPath = $file->storeAs('temp/'.$folder, $filename, 'local');

        if ($storedPath === false) {
            throw new \RuntimeException('Berkas sementara gagal disimpan.');
        }

        $absolutePath = Storage::disk('local')->path($storedPath);

        try {
            $this->resizeLargeImage($absolutePath, $file->getMimeType());
        } catch (\Throwable $exception) {
            Storage::disk('local')->deleteDirectory('temp/'.$folder);

            throw $exception;
        }

        return $this->repository->create([
            'folder' => $folder,
            'filename' => $filename,
        ]);
    }

    /**
     * Phone photos can be several thousand pixels wide even when their
     * compressed file size is small. Downscale them before Media Library
     * creates its synchronous thumbnails to avoid memory spikes on cPanel.
     */
    private function resizeLargeImage(string $path, ?string $mimeType): void
    {
        if (! $mimeType || ! str_starts_with($mimeType, 'image/') || $mimeType === 'image/gif') {
            return;
        }

        $dimensions = @getimagesize($path);

        if (! $dimensions) {
            return;
        }

        [$width, $height] = $dimensions;

        if (max($width, $height) <= self::MAX_IMAGE_DIMENSION) {
            return;
        }

        $image = Image::load($path);

        if ($width >= $height) {
            $image->width(self::MAX_IMAGE_DIMENSION);
        } else {
            $image->height(self::MAX_IMAGE_DIMENSION);
        }

        $image->save($path);
    }

    public function deleteByFolder(string $folder): bool
    {
        $temporaryFile = $this->repository->findByFolder($folder);

        if ($temporaryFile) {
            Storage::disk('local')->deleteDirectory('temp/'.$temporaryFile->folder);

            return $this->repository->delete($temporaryFile);
        }

        return false;
    }

    public function processTemporaryMedia($model, string $folderUuid, string $collectionName = 'default')
    {
        $temporaryFile = $this->repository->findByFolder($folderUuid);

        if ($temporaryFile) {
            $disk = Storage::disk('local');
            $relativePath = 'temp/'.$temporaryFile->folder.'/'.$temporaryFile->filename;
            $path = $disk->path($relativePath);

            if ($disk->exists($relativePath) && is_file($path)) {
                $media = $model->addMedia($path)->toMediaCollection($collectionName);

                $disk->deleteDirectory('temp/'.$temporaryFile->folder);
                $this->repository->delete($temporaryFile);

                return $media;
            }
        }

        return null;
    }

    public function clearExpiredMedia(int $hours = 24): int
    {
        $files = $this->repository->getExpiredFiles($hours);
        $count = 0;

        foreach ($files as $file) {
            Storage::disk('local')->deleteDirectory('temp/'.$file->folder);
            $this->repository->delete($file);
            $count++;
        }

        return $count;
    }
}
