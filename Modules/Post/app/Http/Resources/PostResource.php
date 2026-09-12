<?php

namespace Modules\Post\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\PostContentSanitizer;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => app(PostContentSanitizer::class)->sanitize((string) $this->body),
            'status' => $this->status,
            'published_at' => $this->published_at,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'author' => $this->whenLoaded('author', fn () => $this->author ? [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ] : null),
            'cover' => $cover ? [
                'id' => $cover->id,
                'original' => $cover->getUrl(),
                'thumb' => $cover->getUrl('thumb'),
                'card' => $cover->getUrl('card'),
            ] : null,
            'content_media' => $this->getMedia('content_media')->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
            ])->values()->all(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
