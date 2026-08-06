<?php

namespace Modules\Home\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Product\Http\Resources\ProductCategoryResource;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Services\ProductCategoryService;
use Modules\Product\Services\ProductService;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class HomePageController extends Controller
{
    private const LIMIT = 8;

    public function __construct(
        private readonly ProductService $products,
        private readonly ProductCategoryService $categories,
        private readonly RegionRepositoryInterface $regions,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Home', [
            'featuredProducts' => PublicProductResource::collection($this->products->publicFeatured(self::LIMIT))->resolve(),
            'latestProducts' => PublicProductResource::collection($this->products->publicLatest(self::LIMIT))->resolve(),
            'categories' => ProductCategoryResource::collection($this->categories->orderedList())->resolve(),
            'regions' => PublicRegionResource::collection($this->regions->publicFiltered()->get())->resolve(),
        ]);
    }
}
