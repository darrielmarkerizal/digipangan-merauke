<?php

namespace Modules\Media\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Media\Models\TemporaryFile;

interface TemporaryFileRepositoryInterface
{
    public function create(array $data): TemporaryFile;

    public function findByFolder(string $folder): ?TemporaryFile;

    public function delete(TemporaryFile $file): bool;

    public function getExpiredFiles(int $hours): Collection;
}
