<?php

namespace Modules\Post\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Modules\Post\Http\Requests\StorePostRequest;
use Modules\Post\Http\Requests\UpdatePostRequest;
use Modules\Post\Http\Resources\PostResource;
use Modules\Post\Services\PostCategoryService;
use Modules\Post\Services\PostService;

class PostAdminController extends Controller
{
    public function __construct(
        private readonly PostService $service,
        private readonly PostCategoryService $categoryService,
    ) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/Post/Index',
            $this->service->paginateFiltered(),
            PostResource::class,
            ['authors' => User::select('id', 'name')->get()],
            'posts'
        );
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Post/Create', [
            'categories' => $this->categoryService->list(),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.post.index')
            ->with('success', 'Berita berhasil dibuat.');
    }

    public function show(int $id): Response
    {
        return Inertia::render('Admin/Post/Show', [
            'post' => (new PostResource($this->service->findOrFail($id)))->resolve(),
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Admin/Post/Edit', [
            'post'       => (new PostResource($this->service->findOrFail($id)))->resolve(),
            'categories' => $this->categoryService->list(),
        ]);
    }

    public function update(UpdatePostRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.post.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return redirect()->route('admin.post.index')
            ->with('success', 'Berita berhasil dihapus.');
    }
}
