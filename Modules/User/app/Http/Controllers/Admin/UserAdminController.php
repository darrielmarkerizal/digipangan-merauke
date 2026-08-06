<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\UserService;

class UserAdminController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/User/Index',
            $this->service->paginateFiltered(),
            UserResource::class,
            ['roles' => $this->service->availableRoleNames()],
            'users'
        );
    }

    public function create(): Response
    {
        return Inertia::render('Admin/User/Create', [
            'roles' => $this->service->availableRoleNames(),
        ]);
    }

    public function show(int $id): Response
    {
        return Inertia::render('Admin/User/Show', [
            'user' => (new UserResource($this->service->findOrFail($id, ['roles'])))->resolve(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.user.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id): Response
    {
        return Inertia::render('Admin/User/Edit', [
            'user'  => (new UserResource($this->service->findOrFail($id, ['roles'])))->resolve(),
            'roles' => $this->service->availableRoleNames(),
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.user.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $model = $this->service->findOrFail($id);

        abort_if($model->is($request->user()), 422, 'Tidak dapat menghapus akun sendiri.');

        $this->service->delete($model);

        return redirect()->route('admin.user.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
