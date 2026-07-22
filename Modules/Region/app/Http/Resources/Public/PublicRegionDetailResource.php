<?php

namespace Modules\Region\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Http\Resources\Public\PublicProductResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicRegionDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'agricultural_potential' => $this->agricultural_potential,
            'area_km2' => $this->area_km2,
            'population' => $this->population,
            'villages_count' => (int) ($this->villages_count ?? 0),
            'farmer_groups_count' => (int) ($this->farmer_groups_count ?? 0),
            'products_count' => (int) ($this->active_products_count ?? 0),
            'cover' => $cover ? [
                'original' => $cover->getUrl(),
                'card' => $cover->getUrl('card'),
            ] : null,
            'gallery' => $this->getMedia('gallery')->map(fn (Media $media) => [
                'thumb' => $media->getUrl('thumb'),
                'card' => $media->getUrl('card'),
            ])->all(),
            'featured_products' => $this->whenLoaded(
                'regionFeaturedProducts',
                fn () => PublicProductResource::collection($this->regionFeaturedProducts)
            ),
        ];
    }
}
