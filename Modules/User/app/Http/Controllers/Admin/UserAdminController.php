<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\InertiaQuery;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Region\Services\RegionService;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\UserService;

class UserAdminController extends Controller
{
    public function __construct(
        private readonly UserService $service,
        private readonly RegionService $regionService,
    ) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->hasRole('super_admin'), 403, 'Akses ditolak: Hanya Super Admin yang dapat mengelola pengguna.');

        return InertiaQuery::render(
            'Admin/User/Index',
            $this->service->paginateFiltered(),
            UserResource::class,
            [
                'roles' => $this->service->availableRoleNames(),
                'regions' => $this->regionService->list(),
            ],
            'users'
        );
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()?->hasRole('super_admin'), 403, 'Akses ditolak: Hanya Super Admin yang dapat mengelola pengguna.');

        return Inertia::render('Admin/User/Create', [
            'roles' => $this->service->availableRoleNames(),
            'regions' => $this->regionService->list(),
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        abort_unless($request->user()?->hasRole('super_admin'), 403, 'Akses ditolak: Hanya Super Admin yang dapat mengelola pengguna.');

        return Inertia::render('Admin/User/Show', [
            'user' => (new UserResource($this->service->findOrFail($id, ['roles', 'region'])))->resolve(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->hasRole('super_admin'), 403, 'Akses ditolak: Hanya Super Admin yang dapat mengelola pengguna.');

        $this->service->create($request->validated());

        return redirect()->route('admin.user.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Request $request, int $id): Response
    {
        abort_unless($request->user()?->hasRole('super_admin'), 403, 'Akses ditolak: Hanya Super Admin yang dapat mengelola pengguna.');

        return Inertia::render('Admin/User/Edit', [
            'user'  => (new UserResource($this->service->findOrFail($id, ['roles', 'region'])))->resolve(),
            'roles' => $this->service->availableRoleNames(),
            'regions' => $this->regionService->list(),
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        abort_unless($request->user()?->hasRole('super_admin'), 403, 'Akses ditolak: Hanya Super Admin yang dapat mengelola pengguna.');

        $model = $this->service->findOrFail($id);
        $this->service->update($model, $request->validated());

        return redirect()->route('admin.user.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        abort_unless($request->user()?->hasRole('super_admin'), 403, 'Akses ditolak: Hanya Super Admin yang dapat mengelola pengguna.');

        $model = $this->service->findOrFail($id);

        abort_if($model->is($request->user()), 422, 'Tidak dapat menghapus akun sendiri.');

        $this->service->delete($model);

        return redirect()->route('admin.user.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
