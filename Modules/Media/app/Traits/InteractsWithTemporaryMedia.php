<?php

namespace Modules\Media\Traits;

use Modules\Media\Services\TemporaryMediaService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithTemporaryMedia
{
    public function addMediaFromTemporaryUpload(string $folderUuid, string $collectionName = 'default'): ?Media
    {
        return app(TemporaryMediaService::class)->processTemporaryMedia($this, $folderUuid, $collectionName);
    }
}
