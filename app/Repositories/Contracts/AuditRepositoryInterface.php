<?php

namespace App\Repositories\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface AuditRepositoryInterface extends BaseRepositoryInterface
{
    public function monthlyCounts(Carbon $since): Collection;
}
