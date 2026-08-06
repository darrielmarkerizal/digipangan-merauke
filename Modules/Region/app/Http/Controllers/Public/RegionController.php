<?php

namespace Modules\Region\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Region\Http\Resources\Public\PublicRegionDetailResource;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class RegionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly RegionRepositoryInterface $regions,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function index(): JsonResponse
    {
        $regions = $this->regions->publicFiltered()->get();

        return $this->successResponse(PublicRegionResource::collection($regions));
    }

    public function show(string $slug): JsonResponse
    {
        $region = $this->regions->publicFindBySlugWithFeaturedProducts($slug);

        abort_if($region === null, 404, 'Wilayah tidak ditemukan.');

        return $this->successResponse(new PublicRegionDetailResource($region));
    }

    public function products(string $slug): JsonResponse
    {
        $region = $this->regions->publicFindBySlug($slug);

        abort_if($region === null, 404, 'Wilayah tidak ditemukan.');

        request()->merge([
            'filter' => array_merge((array) request('filter', []), ['region_id' => $region->id]),
        ]);

        $paginator = $this->products->publicPaginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(PublicProductResource::collection($paginator->getCollection())->collection)
        );
    }
}
