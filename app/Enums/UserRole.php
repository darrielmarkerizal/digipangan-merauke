<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case DistrictAdmin = 'admin_distrik';
    case Farmer = 'farmer';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
