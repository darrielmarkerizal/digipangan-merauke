<?php

namespace Modules\Page\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Page\Models\Partner;
use Modules\Page\Repositories\Contracts\PartnerRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return parent::query()->with(['media']);
    }

    protected function visibilityScope(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Active partners for the public "about" page, in curation order.
     */
    public function publicActive(): Collection
    {
        return $this->visibilityScope($this->query())->orderBy('sort_order')->get();
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('is_active'),
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'sort_order', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}
