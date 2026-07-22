<?php

namespace Modules\Post\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');

        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'published_at' => $this->published_at,
            'excerpt' => Str::limit(strip_tags((string) $this->body), 160),
            'cover' => $cover ? [
                'thumb' => $cover->getUrl('thumb'),
                'card' => $cover->getUrl('card'),
            ] : null,
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
        ];
    }
}
