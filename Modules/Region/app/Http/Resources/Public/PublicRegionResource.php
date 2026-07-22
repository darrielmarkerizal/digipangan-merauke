<?php

namespace Modules\Region\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicRegionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'cover' => $cover ? [
                'thumb' => $cover->getUrl('thumb'),
                'card' => $cover->getUrl('card'),
            ] : null,
            'villages_count' => (int) ($this->villages_count ?? 0),
            'farmer_groups_count' => (int) ($this->farmer_groups_count ?? 0),
            'products_count' => (int) ($this->active_products_count ?? 0),
        ];
    }
}
