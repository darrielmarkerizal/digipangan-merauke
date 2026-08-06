<?php

namespace Modules\Region\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

use Modules\Region\Http\Resources\Public\PublicRegionDetailResource;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class RegionPageController extends Controller
{
    public function __construct(
        private readonly RegionRepositoryInterface $regions
    ) {}

    public function index(): Response
    {
        $regions = $this->regions->publicFiltered()->get();

        return Inertia::render('Region/Index', [
            'regions' => PublicRegionResource::collection($regions)->resolve(),
        ]);
    }

    public function show(string $slug): Response
    {
        $region = $this->regions->publicFindBySlugWithFeaturedProducts($slug);

        abort_if($region === null, 404, 'Wilayah tidak ditemukan.');

        return Inertia::render('Region/Show', [
            'region' => (new PublicRegionDetailResource($region))->resolve(),
        ]);
    }
}
