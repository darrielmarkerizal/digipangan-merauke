<?php

namespace Modules\Post\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicPostDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');

        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'published_at' => $this->published_at,
            'body' => $this->body,
            'cover' => $cover ? [
                'original' => $cover->getUrl(),
                'card' => $cover->getUrl('card'),
            ] : null,
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'author' => $this->whenLoaded('author', fn () => $this->author ? [
                'name' => $this->author->name,
            ] : null),
        ];
    }
}
