<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Partner;
use Modules\Page\Models\SiteSetting;

class PageDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['name' => 'Universitas Gadjah Mada', 'website_url' => 'https://ugm.ac.id', 'sort_order' => 1],
            ['name' => 'Kementerian Transmigrasi RI', 'website_url' => null, 'sort_order' => 2],
        ];

        foreach ($partners as $partner) {
            Partner::firstOrCreate(['name' => $partner['name']], $partner);
        }

        $settings = [
            ['key' => 'about_background', 'type' => 'richtext'],
            ['key' => 'about_purpose', 'type' => 'richtext'],
            ['key' => 'admin_contact_name', 'type' => 'text'],
            ['key' => 'admin_contact_phone', 'type' => 'phone'],
            ['key' => 'admin_contact_email', 'type' => 'email'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
