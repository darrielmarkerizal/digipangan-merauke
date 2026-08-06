<?php

namespace Modules\Page\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Modules\Page\Models\SiteSetting;
use Modules\Page\Repositories\Contracts\SiteSettingRepositoryInterface;

class SiteSettingRepository extends BaseRepository implements SiteSettingRepositoryInterface
{
    public function __construct(SiteSetting $model)
    {
        parent::__construct($model);
    }

    protected function defaultSort(): string
    {
        return 'key';
    }

    public function allOrderedByKey(): Collection
    {
        return $this->model->newQuery()->orderBy('key')->get();
    }

    public function upsert(string $key, string $value, string $type): void
    {
        $this->model->newQuery()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type],
        );
    }
}
