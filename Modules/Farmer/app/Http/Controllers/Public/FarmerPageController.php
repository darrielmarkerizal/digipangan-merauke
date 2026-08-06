<?php

namespace Modules\Farmer\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Farmer\Http\Resources\Public\PublicFarmerListResource;
use Modules\Farmer\Http\Resources\Public\PublicFarmerResource;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;
use Modules\Region\Http\Resources\Public\PublicRegionResource;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class FarmerPageController extends Controller
{
    public function __construct(
        private readonly FarmerRepositoryInterface $farmers,
        private readonly RegionRepositoryInterface $regions,
    ) {}

    public function index(Request $request): Response
    {
        $regionSlug = $request->string('wilayah')->toString() ?: null;
        $search = $request->string('q')->toString() ?: null;

        $paginator = $this->farmers->publicListForDirectory($regionSlug, $search);

        $regions = $this->regions->publicFiltered()->get();

        return Inertia::render('Farmer/Index', [
            'farmers' => PublicFarmerListResource::collection($paginator),
            'regions' => PublicRegionResource::collection($regions)->resolve(),
            'filters' => (object) [
                'wilayah' => $regionSlug,
                'q' => $search,
            ],
        ]);
    }

    public function show(string $slug): Response
    {
        $farmer = $this->farmers->publicFindBySlugWithProducts($slug);

        abort_if($farmer === null, 404, 'Petani tidak ditemukan.');

        return Inertia::render('Farmer/Show', [
            'farmer' => (new PublicFarmerResource($farmer))->resolve(),
        ]);
    }
}
