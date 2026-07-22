<?php

namespace Modules\Post\Http\Resources\Public;

use App\Support\PublicUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicPostDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');
        $ogImage = $cover?->getUrl('card');
        $description = Str::limit(strip_tags((string) $this->body), 160);

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
            'seo' => [
                'title' => $this->title.' — DigiPangan Merauke',
                'description' => $description,
                'canonical' => PublicUrl::post($this->slug),
                'og_image' => $ogImage,
                'structured_data' => array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $this->title,
                    'description' => $description,
                    'image' => $ogImage,
                    'datePublished' => $this->published_at?->toAtomString(),
                    'author' => $this->relationLoaded('author') && $this->author ? [
                        '@type' => 'Person',
                        'name' => $this->author->name,
                    ] : null,
                ], fn ($value) => $value !== null),
            ],
        ];
    }
}
