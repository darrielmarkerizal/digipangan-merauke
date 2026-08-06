<?php

namespace Modules\Product\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface ProductInteractionRepositoryInterface extends BaseRepositoryInterface
{
    public function existsViewToday(int $productId, string $visitorHash): bool;

    public function countByType(string $type): int;

    public function monthlyCountsByType(string $type, Carbon $since): SupportCollection;

    public function recentContactsWithProduct(int $limit = 5): Collection;
}
