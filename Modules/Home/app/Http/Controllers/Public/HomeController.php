<?php

namespace Modules\Home\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Models\Product;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class HomeController extends Controller
{
    use ApiResponse;

    private const LIMIT = 8;

    public function __construct(private readonly RegionRepositoryInterface $regions) {}

    public function index(): JsonResponse
    {
        $with = ['media', 'category', 'farmer', 'region'];

        $featured = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with($with)
            ->latest()
            ->limit(self::LIMIT)
            ->get();

        $latest = Product::query()
            ->where('is_active', true)
            ->with($with)
            ->latest()
            ->limit(self::LIMIT)
            ->get();

        return $this->successResponse([
            'featured_products' => PublicProductResource::collection($featured),
            'latest_products' => PublicProductResource::collection($latest),
            'regions' => PublicRegionResource::collection($this->regions->publicFiltered()->get()),
        ]);
    }
}
