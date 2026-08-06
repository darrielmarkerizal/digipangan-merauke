<?php

namespace Modules\Page\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Page\Http\Resources\FaqResource;
use Modules\Page\Http\Resources\PartnerResource;
use Modules\Page\Services\FaqService;
use Modules\Page\Services\PartnerService;
use Modules\Page\Services\SiteSettingService;

class AboutPageController extends Controller
{
    public function __construct(
        private readonly SiteSettingService $settings,
        private readonly PartnerService $partners,
        private readonly FaqService $faqs,
    ) {}

    public function show(): Response
    {
        $settings = $this->settings->all()
            ->mapWithKeys(fn ($setting) => [$setting->key => $setting->value]);

        return Inertia::render('About/Index', [
            'settings' => $settings,
            'partners' => PartnerResource::collection($this->partners->publicActive())->resolve(),
            'faqs' => FaqResource::collection($this->faqs->publicActive())->resolve(),
        ]);
    }
}
