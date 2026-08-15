<?php

namespace App\Enums;

enum Permission: string
{
    case ManageUsers = 'user.kelola';
    case ManageProducts = 'produk.kelola';
    case ManageFarmers = 'petani.kelola';
    case ManageRegions = 'wilayah.kelola';
    case ManagePosts = 'berita.kelola';
    case ManageMasterData = 'master.kelola';
    case ManageAboutPage = 'tentang.kelola';
    case ViewStatistics = 'statistik.lihat';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function forAdmin(): array
    {
        return array_values(array_diff(self::values(), [self::ManageUsers->value]));
    }

    public static function forDistrictAdmin(): array
    {
        return [
            self::ManageProducts->value,
            self::ManageFarmers->value,
            self::ManageRegions->value,
            self::ViewStatistics->value,
            self::ManagePosts->value,
        ];
    }
}
