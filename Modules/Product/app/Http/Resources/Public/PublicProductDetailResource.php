<?php

namespace Modules\Product\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'weight_value' => $this->weight_value,
            'stock_available' => $this->stock_available,
            'unit' => $this->whenLoaded('unit', fn () => [
                'name' => $this->unit->name,
                'symbol' => $this->unit->symbol,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'region' => $this->whenLoaded('region', fn () => [
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ]),
            'farmer' => $this->whenLoaded('farmer', fn () => [
                'name' => $this->farmer->name,
                'slug' => $this->farmer->slug,
                'phone' => $this->farmer->phone,
            ]),
            'photos' => $this->getMedia('photos')->map(fn (Media $media) => [
                'original' => $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
                'card' => $media->getUrl('card'),
            ])->all(),
        ];
    }
}
