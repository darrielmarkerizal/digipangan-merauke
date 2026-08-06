<?php

namespace App\Repositories;

use App\Repositories\Contracts\AuditRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Models\Audit;
use Spatie\QueryBuilder\AllowedFilter;

class AuditRepository extends BaseRepository implements AuditRepositoryInterface
{
    public function __construct(Audit $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return parent::query()->with('user');
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::exact('event'),
            AllowedFilter::partial('auditable_type'),
        ];
    }

    protected function allowedSorts(): array
    {
        return ['created_at', 'event'];
    }

    /**
     * Audit counts per calendar month, keyed 'Y-m', for the statistics
     * dashboard's admin-activity trend.
     */
    public function monthlyCounts(Carbon $since): Collection
    {
        return $this->model->newQuery()
            ->whereNotNull('user_id')
            ->where('created_at', '>=', $since)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as aggregate")
            ->groupBy('ym')
            ->pluck('aggregate', 'ym');
    }
}
