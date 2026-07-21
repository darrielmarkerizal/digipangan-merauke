<?php

namespace Modules\Region\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Product\Models\Product;
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
    'name',
    'description',
    'agricultural_potential',
    'area_km2',
    'population',
    'is_active',
])]
class Region extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasSlug, InteractsWithMedia, TemporaryMediaTrait, SoftDeletes;

    protected function casts(): array
    {
        return [
            'area_km2' => 'decimal:2',
            'population' => 'integer',
            'is_active' => 'boolean',
        ];
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
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 400, 400)->nonQueued();
        $this->addMediaConversion('card')->width(800)->nonQueued();
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function farmerGroups(): HasMany
    {
        return $this->hasMany(FarmerGroup::class);
    }

    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
