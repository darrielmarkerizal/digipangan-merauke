<?php

namespace Modules\Farmer\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\Farmer\Http\Requests\StoreCommodityRequest;
use Modules\Farmer\Http\Requests\UpdateCommodityRequest;
use Modules\Farmer\Http\Resources\CommodityResource;
use Modules\Farmer\Services\CommodityService;

class CommodityAdminController extends Controller
{
    public function __construct(private readonly CommodityService $service) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Commodity/Index',
            $this->service->paginateFiltered(),
            CommodityResource::class,
            [],
            'commodities'
        );
    }

    public function store(StoreCommodityRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->back()
            ->with('success', 'Komoditas berhasil ditambahkan.');
    }

    public function update(UpdateCommodityRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->back()
            ->with('success', 'Komoditas berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->back()
            ->with('success', 'Komoditas berhasil dihapus.');
    }
}
