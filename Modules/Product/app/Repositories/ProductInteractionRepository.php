<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Modules\Product\Enums\ProductInteractionType;
use Modules\Product\Models\ProductInteraction;
use Modules\Product\Repositories\Contracts\ProductInteractionRepositoryInterface;

class ProductInteractionRepository extends BaseRepository implements ProductInteractionRepositoryInterface
{
    public function __construct(ProductInteraction $model)
    {
        parent::__construct($model);
    }

    public function existsViewToday(int $productId, string $visitorHash): bool
    {
        return $this->model->newQuery()
            ->where('product_id', $productId)
            ->where('type', ProductInteractionType::View)
            ->where('visitor_hash', $visitorHash)
            ->whereDate('occurred_at', now()->toDateString())
            ->exists();
    }

    public function countByType(ProductInteractionType $type, ?int $regionId = null): int
    {
        $query = $this->model->newQuery()->where('type', $type);

        if ($regionId !== null) {
            $query->whereHas('product', fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('region_id', $regionId));
        }

        return $query->count();
    }

    /**
     * Interaction counts of a given type per calendar month, keyed 'Y-m',
     * for monthly trend charts.
     */
    public function monthlyCountsByType(ProductInteractionType $type, Carbon $since, ?int $regionId = null): SupportCollection
    {
        $query = $this->model->newQuery()
            ->where('type', $type)
            ->where('occurred_at', '>=', $since);

        if ($regionId !== null) {
            $query->whereHas('product', fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('region_id', $regionId));
        }

        return $query
            ->selectRaw("DATE_FORMAT(occurred_at, '%Y-%m') as ym, COUNT(*) as aggregate")
            ->groupBy('ym')
            ->pluck('aggregate', 'ym');
    }

    /**
     * Most recent contact-click interactions with their product, for the
     * dashboard's recent-activity feed.
     */
    public function recentContactsWithProduct(int $limit = 5, ?int $regionId = null): Collection
    {
        $query = $this->model->newQuery()
            ->with(['product:id,name'])
            ->where('type', ProductInteractionType::Contact);

        if ($regionId !== null) {
            $query->whereHas('product', fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('region_id', $regionId));
        }

        return $query
            ->orderByDesc('occurred_at')
            ->take($limit)
            ->get();
    }
}
