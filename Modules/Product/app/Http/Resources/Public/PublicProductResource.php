<?php

namespace Modules\Product\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photo = $this->getMedia('photos')->first();

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'stock_available' => $this->stock_available,
            'photo' => $photo ? [
                'thumb' => $photo->getUrl('thumb'),
                'card' => $photo->getUrl('card'),
            ] : null,
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
            ]),
        ];
    }
}
