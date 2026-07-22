<?php

namespace App\Support;

/**
 * Builds absolute URLs to the public-facing frontend pages. Centralises the
 * frontend path scheme so the sitemap and SEO metadata stay consistent.
 */
class PublicUrl
{
    public static function base(): string
    {
        return rtrim((string) config('digipangan.frontend_url'), '/');
    }

    public static function home(): string
    {
        return self::base().'/';
    }

    public static function products(): string
    {
        return self::base().'/produk';
    }

    public static function product(string $slug): string
    {
        return self::base().'/produk/'.$slug;
    }

    public static function region(string $slug): string
    {
        return self::base().'/wilayah/'.$slug;
    }

    public static function farmer(string $slug): string
    {
        return self::base().'/petani/'.$slug;
    }

    public static function posts(): string
    {
        return self::base().'/berita';
    }

    public static function post(string $slug): string
    {
        return self::base().'/berita/'.$slug;
    }

    public static function about(): string
    {
        return self::base().'/tentang';
    }
}
