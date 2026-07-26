<?php

namespace Modules\Home\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Models\Product;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class HomePageController extends Controller
{
    private const LIMIT = 8;

    public function __construct(private readonly RegionRepositoryInterface $regions) {}

    public function index(): Response
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

        return Inertia::render('Home', [
            'featuredProducts' => PublicProductResource::collection($featured)->resolve(),
            'latestProducts' => PublicProductResource::collection($latest)->resolve(),
            'regions' => PublicRegionResource::collection($this->regions->publicFiltered()->get())->resolve(),
        ]);
    }
}
