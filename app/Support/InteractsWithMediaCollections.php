<?php

namespace App\Support;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Standard media collections + conversions untuk model apapun.
 *
 * Cara pakai:
 *  1. Model implement Spatie\MediaLibrary\HasMedia + use InteractsWithMedia
 *  2. Model juga use InteractsWithMediaCollections
 *  3. Panggil $this->registerStandardCollections('gallery') dst di
 *     registerMediaCollections() bila perlu koleksi custom.
 */
trait InteractsWithMediaCollections
{
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->useDisk(config('digipangan.media_disk', 'public'));

        $this->addMediaCollection('gallery')
            ->useDisk(config('digipangan.media_disk', 'public'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 300)
            ->nonQueued();

        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 600, 400)
            ->nonQueued();

        $this->addMediaConversion('full')
            ->fit(Fit::Contain, 1600, 1600)
            ->nonQueued();
    }

    public function coverUrl(string $conversion = 'card'): ?string
    {
        $media = $this->getFirstMedia('cover');

        return $media?->getUrl($conversion) ?: $media?->getUrl();
    }

    public function galleryUrls(string $conversion = 'card'): array
    {
        return $this->getMedia('gallery')
            ->map(fn (Media $m) => $m->getUrl($conversion) ?: $m->getUrl())
            ->all();
    }
}
