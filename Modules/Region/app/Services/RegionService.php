<?php

namespace Modules\Region\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class RegionService extends BaseService
{
    public function __construct(RegionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $cover = Arr::pull($data, 'cover');
        $gallery = Arr::pull($data, 'gallery', []);

        return DB::transaction(function () use ($data, $cover, $gallery) {
            $region = $this->repository->create($data);
            $this->attachCover($region, $cover);
            $this->syncGallery($region, $gallery, null);

            return $this->repository->findOrFail($region->getKey());
        });
    }

    public function update(Model $model, array $data): Model
    {
        $cover = Arr::pull($data, 'cover');
        $gallery = Arr::pull($data, 'gallery', null);
        $retained = Arr::pull($data, 'retained_gallery', null);

        return DB::transaction(function () use ($model, $data, $cover, $gallery, $retained) {
            $region = $this->repository->update($model, $data);
            $this->attachCover($region, $cover);
            $this->syncGallery($region, $gallery, $retained);

            return $this->repository->findOrFail($region->getKey());
        });
    }

    private function attachCover(Model $region, ?string $cover): void
    {
        if ($cover) {
            $region->addMediaFromTemporaryUpload($cover, 'cover');
        }
    }

    /**
     * Sync the `gallery` collection. When $retainedIds is an array, existing
     * media not listed are removed; when null, existing media are kept.
     * New temporary uploads in $newFolders are always appended.
     */
    private function syncGallery(Model $region, ?array $newFolders, ?array $retainedIds): void
    {
        if (is_array($retainedIds)) {
            $region->getMedia('gallery')
                ->reject(fn ($media) => in_array($media->id, $retainedIds))
                ->each(fn ($media) => $media->delete());
        }

        foreach ($newFolders ?? [] as $folder) {
            $region->addMediaFromTemporaryUpload($folder, 'gallery');
        }
    }
}
