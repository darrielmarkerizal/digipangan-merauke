<?php

namespace Modules\Region\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RegionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'agricultural_potential' => $this->agricultural_potential,
            'area_km2' => $this->area_km2,
            'population' => $this->population,
            'is_active' => $this->is_active,
            'villages_count' => $this->whenCounted('villages'),
            'farmer_groups_count' => $this->whenCounted('farmerGroups'),
            'cover' => $cover ? [
                'id' => $cover->id,
                'original' => $cover->getUrl(),
                'thumb' => $cover->getUrl('thumb'),
                'card' => $cover->getUrl('card'),
            ] : null,
            'gallery' => $this->getMedia('gallery')->map(fn (Media $media) => [
                'id' => $media->id,
                'original' => $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
                'card' => $media->getUrl('card'),
            ])->all(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
