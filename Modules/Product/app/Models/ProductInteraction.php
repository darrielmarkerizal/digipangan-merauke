<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Enums\ProductInteractionType;
use Modules\Region\Models\Region;

#[Fillable([
    'product_id',
    'region_id',
    'type',
    'visitor_hash',
    'referrer_host',
    'occurred_at',
])]
class ProductInteraction extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'type' => ProductInteractionType::class,
            'occurred_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
