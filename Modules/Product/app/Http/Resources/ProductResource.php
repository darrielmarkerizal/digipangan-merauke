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
            'product_category_id' => $this->product_category_id,
            'unit_id' => $this->unit_id,
            'farmer_id' => $this->farmer_id,
            'region_id' => $this->region_id,
            'stock_available' => $this->stock_available,
            'is_featured' => $this->is_featured,
            'is_region_featured' => $this->is_region_featured,
            'is_active' => $this->is_active,
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null),
            'unit' => $this->whenLoaded('unit', fn () => $this->unit ? [
                'id' => $this->unit->id,
                'name' => $this->unit->name,
                'symbol' => $this->unit->symbol,
            ] : null),
            'farmer' => $this->whenLoaded('farmer', fn () => $this->farmer ? [
                'id' => $this->farmer->id,
                'name' => $this->farmer->name,
                'slug' => $this->farmer->slug,
                'phone' => $this->farmer->phone,
            ] : null),
            'region' => $this->whenLoaded('region', fn () => $this->region ? [
                'id' => $this->region->id,
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ] : null),
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
