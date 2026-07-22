<?php

namespace Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'weight_value' => $this->weight_value,
            'stock_available' => $this->stock_available,
            'is_featured' => $this->is_featured,
            'is_region_featured' => $this->is_region_featured,
            'is_active' => $this->is_active,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'unit' => $this->whenLoaded('unit', fn () => [
                'id' => $this->unit->id,
                'name' => $this->unit->name,
                'symbol' => $this->unit->symbol,
            ]),
            'farmer' => $this->whenLoaded('farmer', fn () => [
                'id' => $this->farmer->id,
                'name' => $this->farmer->name,
                'slug' => $this->farmer->slug,
                'phone' => $this->farmer->phone,
            ]),
            'region' => $this->whenLoaded('region', fn () => [
                'id' => $this->region->id,
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ]),
            'photos' => $this->getMedia('photos')->map(fn (Media $media) => [
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
