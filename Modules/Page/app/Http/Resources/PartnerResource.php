<?php

namespace Modules\Page\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $logo = $this->getFirstMedia('logo');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'website_url' => $this->website_url,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'logo' => $logo ? [
                'id' => $logo->id,
                'original' => $logo->getUrl(),
                'thumb' => $logo->getUrl('thumb'),
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
