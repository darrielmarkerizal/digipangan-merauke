<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Dashboard\Http\Resources\UncontactedProductResource;
use Modules\Dashboard\Services\StatisticsService;

class StatisticsController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly StatisticsService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ViewStatistics->value];
    }

    public function summary(): JsonResponse
    {
        return $this->successResponse($this->service->summary());
    }

    public function uncontactedProducts(Request $request): JsonResponse
    {
        $perPage = max(min($request->integer('per_page', 15), 100), 1);

        $paginator = $this->service->uncontactedProducts($perPage);

        return $this->paginatedResponse(
            $paginator->setCollection(
                UncontactedProductResource::collection($paginator->getCollection())->collection
            )
        );
    }
}
