<?php

namespace Modules\Farmer\Http\Resources\Public;

use App\Support\PublicUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Http\Resources\Public\PublicProductResource;

class PublicFarmerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photo = $this->getFirstMedia('photo');

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'phone' => $this->phone,
            'land_area_ha' => $this->land_area_ha,
            'photo' => $photo ? [
                'original' => $photo->getUrl(),
                'thumb' => $photo->getUrl('thumb'),
                'card' => $photo->getUrl('card'),
            ] : null,
            'region' => $this->whenLoaded('region', fn () => [
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ]),
            'village' => $this->whenLoaded('village', fn () => $this->village ? [
                'name' => $this->village->name,
            ] : null),
            'farmer_group' => $this->whenLoaded('farmerGroup', fn () => $this->farmerGroup ? [
                'name' => $this->farmerGroup->name,
            ] : null),
            'commodities' => $this->whenLoaded('commodities', fn () => $this->commodities->map(fn ($commodity) => [
                'name' => $commodity->name,
                'slug' => $commodity->slug,
            ])->all()),
            'products' => $this->whenLoaded('products', fn () => PublicProductResource::collection($this->products)->resolve()),
            'seo' => [
                'title' => $this->name.' — DigiPangan Merauke',
                'description' => 'Profil petani '.$this->name
                    .($this->relationLoaded('region') && $this->region ? ' dari '.$this->region->name : '')
                    .' beserta produk yang dijual.',
                'canonical' => PublicUrl::farmer($this->slug),
                'og_image' => $photo?->getUrl('card'),
            ],
        ];
    }
}
