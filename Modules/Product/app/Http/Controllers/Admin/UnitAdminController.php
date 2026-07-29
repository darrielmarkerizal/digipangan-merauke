<?php

namespace Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\Product\Http\Requests\StoreUnitRequest;
use Modules\Product\Http\Requests\UpdateUnitRequest;
use Modules\Product\Http\Resources\UnitResource;
use Modules\Product\Services\UnitService;

class UnitAdminController extends Controller
{
    public function __construct(private readonly UnitService $service) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Unit/Index',
            $this->service->paginateFiltered(),
            UnitResource::class,
            [],
            'units'
        );
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->back()
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function update(UpdateUnitRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->back()
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->back()
            ->with('success', 'Satuan berhasil dihapus.');
    }
}
