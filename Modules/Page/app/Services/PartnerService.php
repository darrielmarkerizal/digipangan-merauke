<?php

namespace Modules\Page\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Page\Repositories\Contracts\PartnerRepositoryInterface;

class PartnerService extends BaseService
{
    public function __construct(private readonly PartnerRepositoryInterface $partners)
    {
        parent::__construct($partners);
    }

    public function publicActive(): Collection
    {
        return $this->partners->publicActive();
    }

    public function create(array $data): Model
    {
        $logo = Arr::pull($data, 'logo');

        return DB::transaction(function () use ($data, $logo) {
            $partner = $this->repository->create($data);
            $this->attachLogo($partner, $logo);

            return $this->repository->findOrFail($partner->getKey());
        });
    }

    public function update(Model $model, array $data): Model
    {
        $logo = Arr::pull($data, 'logo');

        return DB::transaction(function () use ($model, $data, $logo) {
            $partner = $this->repository->update($model, $data);
            $this->attachLogo($partner, $logo);

            return $this->repository->findOrFail($partner->getKey());
        });
    }

    private function attachLogo(Model $partner, ?string $logo): void
    {
        if ($logo) {
            $partner->addMediaFromTemporaryUpload($logo, 'logo');
        }
    }
}
