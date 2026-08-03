<?php

namespace Modules\Product\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Product\Http\Resources\ProductCategoryResource;
use Modules\Product\Http\Resources\Public\PublicProductDetailResource;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Region\Models\Region;

class ProductPageController extends Controller
{
    public function __construct(private readonly ProductRepositoryInterface $products) {}

    public function index(Request $request): Response
    {
        $paginator = $this->products->publicPaginateFiltered();
        
        $categories = ProductCategory::query()
            ->orderBy('sort_order')
            ->get();

        $regions = Region::query()
            ->where('is_active', true)
            ->select(['id', 'name', 'slug'])
            ->get();

        return Inertia::render('Product/Index', [
            'products' => PublicProductResource::collection($paginator),
            'categories' => ProductCategoryResource::collection($categories)->resolve(),
            'regions' => $regions,
            'filters' => $request->only(['kategori', 'region', 'sort', 'q']),
        ]);
    }

    public function show(string $slug): Response
    {
        $product = $this->products->publicFindBySlug($slug);

        abort_if($product === null, 404, 'Produk tidak ditemukan.');

        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                if ($product->product_category_id) {
                    $query->where('product_category_id', $product->product_category_id);
                }
                if ($product->region_id) {
                    $query->orWhere('region_id', $product->region_id);
                }
            })
            ->with(['region', 'category', 'farmer', 'media'])
            ->latest()
            ->take(4)
            ->get();

        return Inertia::render('Product/Show', [
            'product' => (new PublicProductDetailResource($product))->resolve(),
            'relatedProducts' => PublicProductResource::collection($relatedProducts)->resolve(),
        ]);
    }
}
