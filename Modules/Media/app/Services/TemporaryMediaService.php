<?php

namespace Modules\Media\Services;

use Modules\Media\Repositories\TemporaryFileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Models\TemporaryFile;

class TemporaryMediaService
{
    public function __construct(
        private TemporaryFileRepositoryInterface $repository
    ) {}

    public function handleUpload(UploadedFile $file): TemporaryFile
    {
        $filename = $file->getClientOriginalName();
        $folder = (string) Str::uuid();

        $file->storeAs('temp/' . $folder, $filename);

        return $this->repository->create([
            'folder' => $folder,
            'filename' => $filename,
        ]);
    }

    public function deleteByFolder(string $folder): bool
    {
        $temporaryFile = $this->repository->findByFolder($folder);

        if ($temporaryFile) {
            Storage::deleteDirectory('temp/' . $temporaryFile->folder);
            return $this->repository->delete($temporaryFile);
        }

        return false;
    }

    public function processTemporaryMedia($model, string $folderUuid, string $collectionName = 'default')
    {
        $temporaryFile = $this->repository->findByFolder($folderUuid);

        if ($temporaryFile) {
            $path = Storage::disk('local')->path('temp/' . $temporaryFile->folder . '/' . $temporaryFile->filename);
            
            if (file_exists($path)) {
                $media = $model->addMedia($path)->toMediaCollection($collectionName);
                
                Storage::deleteDirectory('temp/' . $temporaryFile->folder);
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
            Storage::deleteDirectory('temp/' . $file->folder);
            $this->repository->delete($file);
            $count++;
        }

        return $count;
    }
}
