<?php

namespace Modules\Product\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Modules\Product\Enums\ProductInteractionType;

interface ProductInteractionRepositoryInterface extends BaseRepositoryInterface
{
    public function existsViewToday(int $productId, string $visitorHash): bool;

    public function countByType(ProductInteractionType $type): int;

    public function monthlyCountsByType(ProductInteractionType $type, Carbon $since): SupportCollection;

    public function recentContactsWithProduct(int $limit = 5): Collection;
}
