<?php

namespace Modules\Page\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Page\Enums\SiteSettingType;
use Modules\Page\Repositories\Contracts\SiteSettingRepositoryInterface;

class SiteSettingService
{
    /**
     * Canonical setting keys and their storage type. Keys are defined by the
     * application (not user-created); unknown keys are ignored on write.
     */
    private const SCHEMA = [
        'about_background' => SiteSettingType::RichText,
        'about_purpose' => SiteSettingType::RichText,
        'admin_contact_name' => SiteSettingType::Text,
        'admin_contact_phone' => SiteSettingType::Phone,
        'admin_contact_email' => SiteSettingType::Email,
    ];

    public function __construct(private readonly SiteSettingRepositoryInterface $settings) {}

    public function all(): Collection
    {
        return $this->settings->allOrderedByKey();
    }

    /**
     * Update the provided settings (partial). Only known keys are persisted;
     * each row keeps its canonical type.
     */
    public function updateMany(array $values): Collection
    {
        DB::transaction(function () use ($values) {
            foreach ($values as $key => $value) {
                if (! array_key_exists($key, self::SCHEMA)) {
                    continue;
                }

                $this->settings->upsert($key, $value, self::SCHEMA[$key]);
            }
        });

        return $this->all();
    }
}
