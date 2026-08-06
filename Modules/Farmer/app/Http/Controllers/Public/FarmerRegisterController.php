<?php

namespace Modules\Farmer\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Farmer\Http\Requests\StoreFarmerRegistrationRequest;
use Modules\Farmer\Services\FarmerGroupService;
use Modules\Farmer\Services\FarmerRegistrationService;
use Modules\Region\Services\RegionService;
use Modules\Region\Services\VillageService;

class FarmerRegisterController extends Controller
{
    public function __construct(
        private readonly FarmerRegistrationService $registrationService,
        private readonly RegionService $regionService,
        private readonly VillageService $villageService,
        private readonly FarmerGroupService $farmerGroupService,
    ) {}

    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'regions' => $this->regionService->list(),
            'villages' => $this->villageService->list(),
            'farmerGroups' => $this->farmerGroupService->list(),
        ]);
    }

    public function store(StoreFarmerRegistrationRequest $request): RedirectResponse
    {
        $this->registrationService->register($request->validated());

        return redirect()->route('farmer.dashboard.index')
            ->with('success', 'Pendaftaran berhasil. Selamat datang di DigiPangan Merauke.');
    }
}
