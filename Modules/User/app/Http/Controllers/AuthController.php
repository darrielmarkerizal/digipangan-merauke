<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Requests\UpdateProfileRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\AuthService;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request, $request->credentials(), $request->remember());

        return $this->successResponse(new UserResource($user), 'Berhasil masuk.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request);

        return $this->successResponse(null, 'Berhasil keluar.');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($this->authService->profile($request->user()->id))
        );
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->authService->updateProfile($request->user(), $request->validated());

        return $this->successResponse(new UserResource($user), 'Profil diperbarui.');
    }
}
