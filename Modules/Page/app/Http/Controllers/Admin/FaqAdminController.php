<?php

namespace Modules\Page\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\Page\Http\Requests\StoreFaqRequest;
use Modules\Page\Http\Requests\UpdateFaqRequest;
use Modules\Page\Http\Resources\FaqResource;
use Modules\Page\Services\FaqService;

class FaqAdminController extends Controller
{
    public function __construct(private readonly FaqService $service) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Faq/Index',
            $this->service->paginateFiltered(),
            FaqResource::class,
            [],
            'faqs'
        );
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Faq/Create');
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.faq.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Admin/Faq/Edit', [
            'faq' => (new FaqResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function update(UpdateFaqRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->route('admin.faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }
}
