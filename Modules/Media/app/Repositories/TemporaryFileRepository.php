<?php

namespace Modules\Media\Repositories;

use Modules\Media\Models\TemporaryFile;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class TemporaryFileRepository implements TemporaryFileRepositoryInterface
{
    public function create(array $data): TemporaryFile
    {
        return TemporaryFile::create($data);
    }

    public function findByFolder(string $folder): ?TemporaryFile
    {
        return TemporaryFile::where('folder', $folder)->first();
    }

    public function delete(TemporaryFile $file): bool
    {
        return $file->delete();
    }

    public function getExpiredFiles(int $hours): Collection
    {
        return TemporaryFile::where('created_at', '<', Carbon::now()->subHours($hours))->get();
    }
}
