<?php

namespace Modules\Page\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Page\Models\Faq;
use Modules\Page\Models\Partner;
use Modules\Page\Services\SiteSettingService;

class AboutController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SiteSettingService $settings) {}

    public function show(): JsonResponse
    {
        $settings = $this->settings->all()->mapWithKeys(fn ($setting) => [$setting->key => $setting->value]);

        $partners = Partner::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (Partner $partner) {
                $logo = $partner->getFirstMedia('logo');

                return [
                    'name' => $partner->name,
                    'website_url' => $partner->website_url,
                    'description' => $partner->description,
                    'logo' => $logo ? [
                        'thumb' => $logo->getUrl('thumb'),
                        'original' => $logo->getUrl(),
                    ] : null,
                ];
            });

        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Faq $faq) => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ]);

        return $this->successResponse([
            'settings' => $settings,
            'partners' => $partners,
            'faqs' => $faqs,
        ]);
    }
}
