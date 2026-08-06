<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
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
            ->where('type', ProductInteraction::TYPE_VIEW)
            ->where('visitor_hash', $visitorHash)
            ->whereDate('occurred_at', now()->toDateString())
            ->exists();
    }

    public function countByType(string $type): int
    {
        return $this->model->newQuery()->where('type', $type)->count();
    }

    /**
     * Interaction counts of a given type per calendar month, keyed 'Y-m',
     * for monthly trend charts.
     */
    public function monthlyCountsByType(string $type, Carbon $since): SupportCollection
    {
        return $this->model->newQuery()
            ->where('type', $type)
            ->where('occurred_at', '>=', $since)
            ->selectRaw("DATE_FORMAT(occurred_at, '%Y-%m') as ym, COUNT(*) as aggregate")
            ->groupBy('ym')
            ->pluck('aggregate', 'ym');
    }

    /**
     * Most recent contact-click interactions with their product, for the
     * dashboard's recent-activity feed.
     */
    public function recentContactsWithProduct(int $limit = 5): Collection
    {
        return $this->model->newQuery()
            ->with(['product:id,name'])
            ->where('type', ProductInteraction::TYPE_CONTACT)
            ->orderByDesc('occurred_at')
            ->take($limit)
            ->get();
    }
}
