<?php

namespace Modules\Farmer\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicFarmerListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photo = $this->getFirstMedia('photo');

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'land_area_ha' => $this->land_area_ha,
            'products_count' => (int) ($this->products_count ?? 0),
            'photo' => $photo ? [
                'thumb' => $photo->getUrl('thumb'),
                'card' => $photo->getUrl('card'),
            ] : null,
            'region' => $this->whenLoaded('region', fn () => $this->region ? [
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ] : null),
            'farmer_group' => $this->whenLoaded('farmerGroup', fn () => $this->farmerGroup ? [
                'name' => $this->farmerGroup->name,
            ] : null),
            'commodities' => $this->whenLoaded('commodities', fn () => $this->commodities->map(fn ($commodity) => [
                'name' => $commodity->name,
                'slug' => $commodity->slug,
            ])->all()),
        ];
    }
}
