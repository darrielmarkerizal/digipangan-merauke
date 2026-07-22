<?php

namespace Modules\Page\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Page\Models\SiteSetting;

class SiteSettingService
{
    /**
     * Canonical setting keys and their storage type. Keys are defined by the
     * application (not user-created); unknown keys are ignored on write.
     */
    private const SCHEMA = [
        'about_background' => 'richtext',
        'about_purpose' => 'richtext',
        'admin_contact_name' => 'text',
        'admin_contact_phone' => 'phone',
        'admin_contact_email' => 'email',
    ];

    public function all(): Collection
    {
        return SiteSetting::orderBy('key')->get();
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

                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => self::SCHEMA[$key]],
                );
            }
        });

        return $this->all();
    }
}
