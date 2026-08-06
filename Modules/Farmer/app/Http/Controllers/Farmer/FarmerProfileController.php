<?php

namespace Modules\Farmer\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Farmer\Http\Requests\UpdateFarmerProfileRequest;
use Modules\Farmer\Http\Resources\FarmerResource;
use Modules\Farmer\Services\CommodityService;
use Modules\Farmer\Services\FarmerGroupService;
use Modules\Farmer\Services\FarmerService;
use Modules\Region\Services\RegionService;
use Modules\Region\Services\VillageService;

class FarmerProfileController extends Controller
{
    public function __construct(
        private readonly FarmerService $service,
        private readonly RegionService $regionService,
        private readonly VillageService $villageService,
        private readonly FarmerGroupService $farmerGroupService,
        private readonly CommodityService $commodityService,
    ) {}

    public function edit(): Response
    {
        $farmer = $this->currentFarmer();

        return Inertia::render('Farmer/Profile/Edit', [
            'farmer' => (new FarmerResource($this->service->findOrFail($farmer->id, ['media', 'region', 'village', 'farmerGroup', 'commodities'])))->resolve(),
            'regions' => $this->regionService->list(),
            'villages' => $this->villageService->list(),
            'farmerGroups' => $this->farmerGroupService->list(),
            'commodities' => $this->commodityService->list(),
        ]);
    }

    public function update(UpdateFarmerProfileRequest $request): RedirectResponse
    {
        $farmer = $this->currentFarmer();

        $this->service->update($farmer, $request->validated());

        return redirect()->route('farmer.dashboard.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    private function currentFarmer()
    {
        $farmer = auth()->user()->farmer;

        abort_if(! $farmer, 403, 'Profil petani tidak ditemukan untuk akun ini.');

        return $farmer;
    }
}
