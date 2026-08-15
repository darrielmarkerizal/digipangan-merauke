<?php

namespace Modules\Farmer\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;

class FarmerService extends BaseService
{
    public function __construct(private readonly FarmerRepositoryInterface $farmers)
    {
        parent::__construct($farmers);
    }

    public function availableForGroup(int $regionId): Collection
    {
        return $this->farmers->availableForGroup($regionId);
    }

    public function listByRegion(int $regionId): Collection
    {
        return $this->farmers->listByRegion($regionId);
    }

    public function create(array $data): Model
    {
        $photo = Arr::pull($data, 'photo');
        $removePhoto = (bool) Arr::pull($data, 'remove_photo', false);
        $commodities = Arr::pull($data, 'commodities');

        return DB::transaction(function () use ($data, $photo, $removePhoto, $commodities) {
            $farmer = $this->repository->create($data);
            $this->syncRelations($farmer, $photo, $removePhoto, $commodities);

            return $this->repository->findOrFail($farmer->getKey());
        });
    }

    public function update(Model $model, array $data): Model
    {
        $photo = Arr::pull($data, 'photo');
        $removePhoto = (bool) Arr::pull($data, 'remove_photo', false);
        $commodities = Arr::pull($data, 'commodities');

        return DB::transaction(function () use ($model, $data, $photo, $removePhoto, $commodities) {
            $farmer = $this->repository->update($model, $data);
            $this->syncRelations($farmer, $photo, $removePhoto, $commodities);

            return $this->repository->findOrFail($farmer->getKey());
        });
    }

    private function syncRelations(Model $farmer, ?string $photo, bool $removePhoto, ?array $commodities): void
    {
        if ($removePhoto && ! $photo) {
            $farmer->clearMediaCollection('photo');
        }

        if ($photo) {
            $farmer->addMediaFromTemporaryUpload($photo, 'photo');
        }

        if (is_array($commodities)) {
            $farmer->commodities()->sync($commodities);
        }
    }
}
