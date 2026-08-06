<?php

namespace Modules\Home\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Services\ProductService;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class HomeController extends Controller
{
    use ApiResponse;

    private const LIMIT = 8;

    public function __construct(
        private readonly ProductService $products,
        private readonly RegionRepositoryInterface $regions,
    ) {}

    public function index(): JsonResponse
    {
        return $this->successResponse([
            'featured_products' => PublicProductResource::collection($this->products->publicFeatured(self::LIMIT)),
            'latest_products' => PublicProductResource::collection($this->products->publicLatest(self::LIMIT)),
            'regions' => PublicRegionResource::collection($this->regions->publicFiltered()->get()),
        ]);
    }
}
