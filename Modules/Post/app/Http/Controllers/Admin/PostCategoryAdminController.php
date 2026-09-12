<?php

namespace Modules\Post\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Post\Http\Requests\StorePostCategoryRequest;
use Modules\Post\Http\Requests\UpdatePostCategoryRequest;
use Modules\Post\Http\Resources\PostCategoryResource;
use Modules\Post\Services\PostCategoryService;

class PostCategoryAdminController extends Controller
{
    public function __construct(private readonly PostCategoryService $service) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/PostCategory/Index',
            $this->service->paginateFiltered(),
            PostCategoryResource::class,
            [],
            'categories'
        );
    }

    public function store(StorePostCategoryRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->back()->with('success', 'Kategori berita berhasil ditambahkan.');
    }

    public function update(UpdatePostCategoryRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->back()->with('success', 'Kategori berita berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->back()->with('success', 'Kategori berita berhasil dihapus.');
    }
}
