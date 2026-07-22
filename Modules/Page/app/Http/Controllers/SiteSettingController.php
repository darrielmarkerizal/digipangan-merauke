<?php

namespace Modules\Page\Http\Controllers;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Page\Http\Requests\UpdateSiteSettingsRequest;
use Modules\Page\Http\Resources\SiteSettingResource;
use Modules\Page\Services\SiteSettingService;

class SiteSettingController extends Controller implements HasMiddleware
{
    use ApiResponse;

    public function __construct(private readonly SiteSettingService $service) {}

    public static function middleware(): array
    {
        return ['permission:'.Permission::ManageAboutPage->value];
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            SiteSettingResource::collection($this->service->all())
        );
    }

    public function update(UpdateSiteSettingsRequest $request): JsonResponse
    {
        return $this->successResponse(
            SiteSettingResource::collection($this->service->updateMany($request->validated())),
            'Pengaturan situs berhasil diperbarui.'
        );
    }
}
