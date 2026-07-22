<?php

namespace Modules\Product\Http\Resources\Public;

use App\Support\PublicUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $media = $this->getMedia('photos');
        $ogImage = $media->first()?->getUrl('card');
        $description = Str::limit(strip_tags((string) $this->description), 160)
            ?: 'Produk lokal kawasan transmigrasi Merauke.';

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'weight_value' => $this->weight_value,
            'stock_available' => $this->stock_available,
            'unit' => $this->whenLoaded('unit', fn () => [
                'name' => $this->unit->name,
                'symbol' => $this->unit->symbol,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'region' => $this->whenLoaded('region', fn () => [
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ]),
            'farmer' => $this->whenLoaded('farmer', fn () => [
                'name' => $this->farmer->name,
                'slug' => $this->farmer->slug,
                'phone' => $this->farmer->phone,
            ]),
            'photos' => $media->map(fn (Media $item) => [
                'original' => $item->getUrl(),
                'thumb' => $item->getUrl('thumb'),
                'card' => $item->getUrl('card'),
            ])->all(),
            'seo' => [
                'title' => $this->name.' — DigiPangan Merauke',
                'description' => $description,
                'canonical' => PublicUrl::product($this->slug),
                'og_image' => $ogImage,
                'structured_data' => array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'Product',
                    'name' => $this->name,
                    'description' => $description,
                    'image' => $ogImage,
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => (string) $this->price,
                        'priceCurrency' => 'IDR',
                        'availability' => $this->stock_available
                            ? 'https://schema.org/InStock'
                            : 'https://schema.org/OutOfStock',
                        'url' => PublicUrl::product($this->slug),
                    ],
                    'brand' => $this->relationLoaded('farmer') && $this->farmer ? [
                        '@type' => 'Organization',
                        'name' => $this->farmer->name,
                    ] : null,
                ], fn ($value) => $value !== null),
            ],
        ];
    }
}
