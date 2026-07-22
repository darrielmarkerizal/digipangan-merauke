<?php

namespace Modules\Product\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Resources\Public\PublicProductDetailResource;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProductRepositoryInterface $products) {}

    public function index(): JsonResponse
    {
        $paginator = $this->products->publicPaginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(PublicProductResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(string $slug): JsonResponse
    {
        $product = $this->products->publicFindBySlug($slug);

        abort_if($product === null, 404, 'Produk tidak ditemukan.');

        return $this->successResponse(new PublicProductDetailResource($product));
    }
}
