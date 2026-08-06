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

    public function create(array $data): Model
    {
        $photo = Arr::pull($data, 'photo');
        $commodities = Arr::pull($data, 'commodities');

        return DB::transaction(function () use ($data, $photo, $commodities) {
            $farmer = $this->repository->create($data);
            $this->syncRelations($farmer, $photo, $commodities);

            return $this->repository->findOrFail($farmer->getKey());
        });
    }

    public function update(Model $model, array $data): Model
    {
        $photo = Arr::pull($data, 'photo');
        $commodities = Arr::pull($data, 'commodities');

        return DB::transaction(function () use ($model, $data, $photo, $commodities) {
            $farmer = $this->repository->update($model, $data);
            $this->syncRelations($farmer, $photo, $commodities);

            return $this->repository->findOrFail($farmer->getKey());
        });
    }

    private function syncRelations(Model $farmer, ?string $photo, ?array $commodities): void
    {
        if ($photo) {
            $farmer->addMediaFromTemporaryUpload($photo, 'photo');
        }

        if (is_array($commodities)) {
            $farmer->commodities()->sync($commodities);
        }
    }
}
