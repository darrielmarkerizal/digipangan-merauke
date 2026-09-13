<?php

namespace Modules\Media\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Modules\Media\Models\TemporaryFile;

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
