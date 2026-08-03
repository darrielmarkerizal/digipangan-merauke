<?php

namespace Modules\Product\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Product\Http\Resources\ProductCategoryResource;
use Modules\Product\Http\Resources\Public\PublicProductDetailResource;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;

class ProductPageController extends Controller
{
    public function __construct(private readonly ProductRepositoryInterface $products) {}

    public function index(Request $request): Response
    {
        $paginator = $this->products->publicPaginateFiltered();
        
        $categories = ProductCategory::query()
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Product/Index', [
            'products' => PublicProductResource::collection($paginator),
            'categories' => ProductCategoryResource::collection($categories)->resolve(),
            'filters' => $request->only(['kategori', 'q']),
        ]);
    }

    public function show(string $slug): Response
    {
        $product = $this->products->publicFindBySlug($slug);

        abort_if($product === null, 404, 'Produk tidak ditemukan.');

        return Inertia::render('Product/Show', [
            'product' => (new PublicProductDetailResource($product))->resolve(),
        ]);
    }
}
