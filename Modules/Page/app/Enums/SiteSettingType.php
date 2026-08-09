<?php

namespace Modules\Page\Enums;

enum SiteSettingType: string
{
    case Text = 'text';
    case RichText = 'richtext';
    case Phone = 'phone';
    case Email = 'email';
    case Url = 'url';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
