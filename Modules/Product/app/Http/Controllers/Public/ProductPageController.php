<?php

namespace Modules\Product\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Product\Http\Resources\ProductCategoryResource;
use Modules\Product\Http\Resources\Public\PublicProductDetailResource;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Repositories\Contracts\ProductCategoryRepositoryInterface;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class ProductPageController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly ProductCategoryRepositoryInterface $categories,
        private readonly RegionRepositoryInterface $regions,
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->products->publicPaginateFiltered();

        return Inertia::render('Product/Index', [
            'products' => PublicProductResource::collection($paginator),
            'categories' => ProductCategoryResource::collection($this->categories->orderedList())->resolve(),
            'regions' => PublicRegionResource::collection($this->regions->publicFiltered()->get())->resolve(),
            'filters' => (object) $request->only(['kategori', 'region', 'sort', 'q']),
        ]);
    }

    public function show(string $slug): Response
    {
        $product = $this->products->publicFindBySlug($slug);

        abort_if($product === null, 404, 'Produk tidak ditemukan.');

        $relatedProducts = $this->products->publicRelated($product->id, $product->product_category_id, $product->region_id);

        return Inertia::render('Product/Show', [
            'product' => (new PublicProductDetailResource($product))->resolve(),
            'relatedProducts' => PublicProductResource::collection($relatedProducts)->resolve(),
        ]);
    }
}
