<?php

namespace Modules\Media\Repositories;

use Modules\Media\Models\TemporaryFile;
use Illuminate\Database\Eloquent\Collection;

interface TemporaryFileRepositoryInterface
{
    public function create(array $data): TemporaryFile;
    public function findByFolder(string $folder): ?TemporaryFile;
    public function delete(TemporaryFile $file): bool;
    public function getExpiredFiles(int $hours): Collection;
}
