<?php

namespace Modules\Page\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Page\Http\Resources\FaqResource;
use Modules\Page\Http\Resources\PartnerResource;
use Modules\Page\Services\FaqService;
use Modules\Page\Services\PartnerService;
use Modules\Page\Services\SiteSettingService;

class AboutController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SiteSettingService $settings,
        private readonly PartnerService $partners,
        private readonly FaqService $faqs,
    ) {}

    public function show(): JsonResponse
    {
        $settings = $this->settings->all()->mapWithKeys(fn ($setting) => [$setting->key => $setting->value]);

        return $this->successResponse([
            'settings' => $settings,
            'partners' => PartnerResource::collection($this->partners->publicActive()),
            'faqs' => FaqResource::collection($this->faqs->publicActive()),
        ]);
    }
}
