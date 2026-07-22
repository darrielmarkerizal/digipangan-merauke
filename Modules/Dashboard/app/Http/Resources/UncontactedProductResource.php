<?php

namespace Modules\Dashboard\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UncontactedProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'region' => $this->whenLoaded('region', fn () => [
                'id' => $this->region->id,
                'name' => $this->region->name,
            ]),
        ];
    }
}
