<?php

namespace Modules\Product\Enums;

enum ProductInteractionType: string
{
    case View = 'view';
    case Contact = 'contact';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
