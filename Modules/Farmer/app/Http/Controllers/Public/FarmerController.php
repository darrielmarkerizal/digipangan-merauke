<?php

namespace Modules\Farmer\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Farmer\Http\Resources\Public\PublicFarmerResource;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;

class FarmerController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly FarmerRepositoryInterface $farmers) {}

    public function show(string $slug): JsonResponse
    {
        $farmer = $this->farmers->publicFindBySlug($slug);

        abort_if($farmer === null, 404, 'Petani tidak ditemukan.');

        $farmer->load(['products' => fn ($query) => $query
            ->where('is_active', true)
            ->with(['media', 'category', 'region'])
            ->latest()]);

        return $this->successResponse(new PublicFarmerResource($farmer));
    }
}
