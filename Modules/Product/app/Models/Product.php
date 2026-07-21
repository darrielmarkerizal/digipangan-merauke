<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Farmer\Models\Farmer;
use Modules\Region\Models\Region;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\Media\Traits\InteractsWithTemporaryMedia as TemporaryMediaTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[Fillable([
    'product_category_id',
    'unit_id',
    'farmer_id',
    'region_id',
    'name',
    'description',
    'price',
    'weight_value',
    'stock_available',
    'is_featured',
    'is_region_featured',
    'is_active',
])]
class Product extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasSlug, InteractsWithMedia, TemporaryMediaTrait, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'weight_value' => 'decimal:2',
            'stock_available' => 'boolean',
            'is_featured' => 'boolean',
            'is_region_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if ($product->isDirty('farmer_id') || ! $product->region_id) {
                $product->region_id = Farmer::whereKey($product->farmer_id)->value('region_id');
            }
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 400, 400)->nonQueued();
        $this->addMediaConversion('card')->width(800)->nonQueued();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(ProductInteraction::class);
    }
}
