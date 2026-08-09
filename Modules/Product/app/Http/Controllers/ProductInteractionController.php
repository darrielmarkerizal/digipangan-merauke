<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Enums\ProductInteractionType;
use Modules\Product\Models\Product;
use Modules\Product\Services\ProductInteractionService;

class ProductInteractionController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProductInteractionService $service) {}

    public function view(Product $product, Request $request): JsonResponse
    {
        return $this->track($product, ProductInteractionType::View, $request);
    }

    public function contact(Product $product, Request $request): JsonResponse
    {
        return $this->track($product, ProductInteractionType::Contact, $request);
    }

    private function track(Product $product, ProductInteractionType $type, Request $request): JsonResponse
    {
        abort_unless($product->is_active, 404);

        $this->service->recordDeferred($product, $type, $request);

        return $this->successResponse(null, 'Interaksi tercatat.', 202);
    }
}
