<?php

namespace Modules\Farmer\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photo = $this->getFirstMedia('photo');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'phone' => $this->phone,
            'land_area_ha' => $this->land_area_ha,
            'is_active' => $this->is_active,
            'region' => $this->whenLoaded('region', fn () => [
                'id' => $this->region->id,
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ]),
            'village' => $this->whenLoaded('village', fn () => $this->village ? [
                'id' => $this->village->id,
                'name' => $this->village->name,
            ] : null),
            'farmer_group' => $this->whenLoaded('farmerGroup', fn () => $this->farmerGroup ? [
                'id' => $this->farmerGroup->id,
                'name' => $this->farmerGroup->name,
            ] : null),
            'commodities' => $this->whenLoaded('commodities', fn () => $this->commodities->map(fn ($commodity) => [
                'id' => $commodity->id,
                'name' => $commodity->name,
                'slug' => $commodity->slug,
            ])->all()),
            'photo' => $photo ? [
                'id' => $photo->id,
                'original' => $photo->getUrl(),
                'thumb' => $photo->getUrl('thumb'),
                'card' => $photo->getUrl('card'),
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
