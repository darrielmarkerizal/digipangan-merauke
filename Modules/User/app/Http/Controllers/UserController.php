<?php

namespace Modules\User\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\UserService;

class UserController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly UserService $userService) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageUsers->value];
    }

    public function index(): JsonResponse
    {
        $paginator = $this->userService->paginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(UserResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(int $user): JsonResponse
    {
        return $this->successResponse(
            new UserResource($this->userService->findOrFail($user, ['roles']))
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($this->userService->create($request->validated())),
            'Pengguna dibuat.',
            201
        );
    }

    public function update(UpdateUserRequest $request, int $user): JsonResponse
    {
        $model = $this->userService->findOrFail($user);

        return $this->successResponse(
            new UserResource($this->userService->update($model, $request->validated())),
            'Pengguna diperbarui.'
        );
    }

    public function destroy(Request $request, int $user): JsonResponse
    {
        $model = $this->userService->findOrFail($user);

        abort_if($model->is($request->user()), 422, 'Tidak dapat menghapus akun sendiri.');

        $this->userService->delete($model);

        return $this->successResponse(null, 'Pengguna dihapus.');
    }
}
