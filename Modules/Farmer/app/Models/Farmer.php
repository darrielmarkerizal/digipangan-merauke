<?php

namespace Modules\Farmer\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Product\Models\Product;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;
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
    'user_id',
    'region_id',
    'village_id',
    'farmer_group_id',
    'name',
    'phone',
    'land_area_ha',
    'is_active',
])]
class Farmer extends Model implements AuditableContract, HasMedia
{
    use Auditable, HasSlug, InteractsWithMedia, TemporaryMediaTrait, SoftDeletes;

    protected function casts(): array
    {
        return [
            'land_area_ha' => 'decimal:2',
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
        $this->addMediaCollection('photo')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 400, 400)->nonQueued();
        $this->addMediaConversion('card')->width(800)->nonQueued();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function farmerGroup(): BelongsTo
    {
        return $this->belongsTo(FarmerGroup::class);
    }

    public function commodities(): BelongsToMany
    {
        return $this->belongsToMany(Commodity::class, 'farmer_commodity');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
